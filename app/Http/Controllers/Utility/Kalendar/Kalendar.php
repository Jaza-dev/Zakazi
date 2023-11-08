<?php

// Autori: Mateja Milošević 2020/0487

namespace App\Http\Controllers\Utility\Kalendar;

use App\Models\Korisnici\BiznisModel;
use App\Models\Korisnici\ZaposleniModel;
use App\Models\NeradnoVremeModel;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

/**
 * Kalendar – pomoćna klasa koja sadrži funkcije vezane za održavanje i prikaz kalendara na raznim stranicama
 *
 * @version 1.0
 */
class Kalendar {
    /**
     * Dohvatanje svih kalendarskih ulaza (termina i neradnog vremena) za datog zaposlenog u datom biznisu između
     * vremena zadatim parametrima
     *
     * @param BiznisModel $b Biznis za čijeg zaposlenog se dohvataju svi kalendarski ulazi
     * @param ZaposleniModel $zap Zaposleni za kog se dohvataju svi kalendarski ulazi
     * @param int $pocetak Početno vreme perioda za koji se dohvataju kalendarski ulazi
     * @param int $kraj Krajnje vreme perioda za koji se dohvataju kalendarski ulazi
     *
     * @return array
     */
    public static function dohvatiUlaze(BiznisModel $b, ZaposleniModel $zap, int $pocetak, int $kraj) {
        $startSQLDate = date("Y-m-d H:i:s", $pocetak + 1);
        $endSQLDate = date("Y-m-d H:i:s", $kraj - 1);

        $ulazi = [
            "termini" => new Collection(),
            "neradnoVreme" => new Collection()
        ];

        if (!$zap->neradnoVreme->isEmpty()) {
            $ulazi["neradnoVreme"] = $zap->neradnoVreme->toQuery()
                ->where("Biznis_Korisnik_idKor", $b->idKor)
                ->where(function(Builder $query) use ($startSQLDate, $endSQLDate) {
                    $query->whereBetween("vremePocetka", [ $startSQLDate, $endSQLDate ])
                        ->orWhereBetween("vremeKraja", [ $startSQLDate, $endSQLDate ]);
                })
                ->get();
        }

        if (!$zap->termini->isEmpty()) {
            $ulazi["termini"] = $zap->termini->toQuery()
                ->where("Biznis_Korisnik_idKor", $b->idKor)
                ->where(function(Builder $query) use ($startSQLDate, $endSQLDate) {
                    $query->whereBetween("vremePocetka", [ $startSQLDate, $endSQLDate ])
                        ->orWhereBetween("vremeKraja", [ $startSQLDate, $endSQLDate ]);
                })
                ->get();
        }

        return $ulazi;
    }

    /**
     * Dohvatanje svih kalendarskih ulaza (termina i neradnog vremena) za datog zaposlenog u datom biznisu između
     * vremena zadatim parametrima u JSON formatu potrebnom za samo renderovanje kalendara
     *
     * @param BiznisModel $b Biznis za čijeg zaposlenog se dohvataju svi kalendarski ulazi
     * @param ZaposleniModel $zap Zaposleni za kog se dohvataju svi kalendarski ulazi
     * @param int $pocetak Početno vreme perioda za koji se dohvataju kalendarski ulazi
     * @param int $kraj Krajnje vreme perioda za koji se dohvataju kalendarski ulazi
     *
     * @return array
     */
    public static function dohvatiUlazePrikaz(BiznisModel $b, ZaposleniModel $zap, int $pocetak, int $kraj) {
        $ulazi = self::dohvatiUlaze($b, $zap, $pocetak, $kraj);

        $rez = [];

        // MORA ZBOG DEFINE-OVA! HVALA PHP!
        $dummy = new KalendarUlaz("", 0, 0, "", []);
        unset($dummy);

        foreach ($ulazi["neradnoVreme"] as $nVreme) {
            $vremePocetka = strtotime($nVreme->vremePocetka);
            $vremeKraja = strtotime($nVreme->vremeKraja);

            $podaci = [
                "tip" => KTIP_NERADNOVREME,
                "id" => $nVreme->idNeradnoVreme,
                "vremeOd" => date("(d.m.) H:i", $vremePocetka),
                "vremeDo" => date("(d.m.) H:i", $vremeKraja)
            ];

            $rez[] = new KalendarUlaz(
                "Neradno Vreme",
                $vremePocetka * 1000,
                $vremeKraja * 1000,
                "#dc3545",
                $podaci);
        }

        foreach ($ulazi["termini"] as $termin) {
            $vremePocetka = strtotime($termin->vremePocetka);
            $vremeKraja = strtotime($termin->vremeKraja);

            $podaci = [
                "tip" => KTIP_TERMIN,
                "id" => $termin->idTermina,
                "vremeOd" => date("(d.m.) H:i", $vremePocetka),
                "vremeDo" => date("(d.m.) H:i", $vremeKraja),
                "musterija" => $termin->musterija->ime." ".$termin->musterija->prezime,
                "ocena" => $termin->musterija->dohvatiOcenu()
            ];

            $rez[] = new KalendarUlaz(
                $podaci["musterija"]." (".$podaci["ocena"]." ★)",
                $vremePocetka * 1000,
                $vremeKraja * 1000,
                "#0d6efd",
                $podaci);
        }

        return $rez;
    }

    /**
     * Ispitivanje da li bi nastala preklapanja ulaza u kalendaru datog zaposlenog u datom biznisu ukoliko bi se dodao
     * ulaz koji počinje i završava se u vremenima definisanim odgovarajućim parametrima
     *
     * @param BiznisModel $b Biznis za čijeg zaposlenog se ispituju preklapanja
     * @param ZaposleniModel $zap Zaposleni za kog se ispituju preklapanja
     * @param int $pocetak Početno vreme ulaza za koji se ispituju preklapanja
     * @param int $kraj Krajnje vreme ulaza za koji se ispituju preklapanja
     *
     * @return bool
     */
    public static function preklapanje(BiznisModel $b, ZaposleniModel $zap, int $pocetak, int $kraj) {
        $zauzeto = self::dohvatiUlaze($b, $zap, $pocetak, $kraj);

        if (!$zauzeto["neradnoVreme"]->isEmpty() || !$zauzeto["termini"]->isEmpty()) {
            return true;
        }

        $startSQLDate = date("Y-m-d H:i:s", $pocetak);
        $endSQLDate = date("Y-m-d H:i:s", $kraj);

        if (!$zap->neradnoVreme->isEmpty()) {
            $nVremePreklop = $zap->neradnoVreme->toQuery()
                ->where("Biznis_Korisnik_idKor", $b->idKor)
                ->where("vremePocetka", "<=", $startSQLDate)
                ->where("vremeKraja", ">=", $endSQLDate)
                ->get();

            if (!$nVremePreklop->isEmpty()) {
                return true;
            }
        }

        if (!$zap->termini->isEmpty()) {
            $terminiPreklop = $zap->termini->toQuery()
                ->where("Biznis_Korisnik_idKor", $b->idKor)
                ->where("vremePocetka", "<=", $startSQLDate)
                ->where("vremeKraja", ">=", $endSQLDate)
                ->get();

            if (!$terminiPreklop->isEmpty()) {
                return true;
            }
        }

        return false;
    }

    /**
     * Dodavanje zapisa o neradnom vremenu u kalendar datog zaposlenog u datom biznisu (i u bazu) uz spajanje susednih
     * ulaza u kalendaru
     *
     * @param BiznisModel $b Biznis za čijeg zaposlenog se dodaje neradno vreme
     * @param ZaposleniModel $zap Zaposleni za kog se dodaje neradno vreme
     * @param int $pocetak Početak neradnog vremena
     * @param int $kraj Kraj neradnog vremena
     *
     * @return void
     */
    public static function dodajNeradnoVreme(BiznisModel $b, ZaposleniModel $zap, int $pocetak, int $kraj) {
        $startSQLDate = date("Y-m-d H:i:s", $pocetak);
        $endSQLDate = date("Y-m-d H:i:s", $kraj);

        $pre = null;
        $posle = null;

        if (!$zap->neradnoVreme->isEmpty()) {
            $pre = $zap->neradnoVreme->toQuery()
                ->where("Biznis_Korisnik_idKor", $b->idKor)
                ->where("vremeKraja", $startSQLDate)
                ->first();

            $posle = $zap->neradnoVreme->toQuery()
                ->where("Biznis_Korisnik_idKor", $b->idKor)
                ->where("vremePocetka", $endSQLDate)
                ->first();
        }

        $nVreme = null;

        if ($pre != null) {
            if ($posle == null) {
                $pre->vremeKraja = $endSQLDate;
            } else {
                $pre->vremeKraja = $posle->vremeKraja;

                $posle->delete();
                $posle = null;
            }

            $nVreme = $pre;
        } else if ($posle != null) {
            $posle->vremePocetka = $startSQLDate;

            $nVreme = $posle;
        }

        if ($nVreme == null) {
            $nVreme = new NeradnoVremeModel();

            $nVreme->biznis()->associate($b);
            $nVreme->zaposleni()->associate($zap);

            $nVreme->vremePocetka = $startSQLDate;
            $nVreme->vremeKraja = $endSQLDate;

            $nVreme->trajanje = ($kraj - $pocetak) / 60;
        }

        $nVreme->push();
    }
}
