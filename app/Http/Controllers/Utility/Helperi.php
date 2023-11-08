<?php

// Autori: Mateja Milošević 2020/0487

namespace App\Http\Controllers\Utility;

use App\Mail\PotvrdaZaposlenja;
use App\Mail\VerifikacijaIzmena;
use App\Mail\VerifikacijaRegistracije;
use App\Models\Korisnici\AdministratorModel;
use App\Models\Korisnici\BiznisModel;
use App\Models\Korisnici\KorisnikModel;
use App\Models\Korisnici\MusterijaModel;
use App\Models\Korisnici\ZaposleniModel;
use App\Models\ZaposlenjeModel;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

/**
 * Klasa koja sadrži pomoćne funkcije
 *
 * @version 1.0
 */
class Helperi {
    /**
     * Dohvatanje trenutno autentifikovanog korisnika
     *
     * @return AdministratorModel|BiznisModel|MusterijaModel|ZaposleniModel|null
     */
    public static function dohvatiAuthKorisnika() {
        if (session("authGuard") == null) {
            return null;
        }

        return Auth::guard(session("authGuard"))->user()->dohvatiSpecijalizaciju();
    }

    /**
     * Iniciranje procesa mejl verifikacije za registraciju
     *
     * @param KorisnikModel $k Novokreirani korisnik za koga se vrši verifikacija mejl adrese
     *
     * @return void
     */
    public static function posaljiMejlVerifikacijuReg(KorisnikModel $k) {
        $hash = md5(microtime());

        DB::table("mejlverifikacija")->insert([
            "hash" => $hash,
            "Korisnik_idKor" => $k->idKor
        ]);

        Mail::to($k->email)->send(new VerifikacijaRegistracije($hash));
    }

    /**
     * Iniciranje procesa mejl verifikacije za izmenu podataka
     *
     * Verifikacioni mejl će biti poslat na novu mejl adresu korisnika.
     *
     * @param KorisnikModel $k Korisnik za koga se vrši verifikacija izmene podataka
     *
     * @return void
     */
    public static function posaljiMejlVerifikacijuIzmena(KorisnikModel $k) {
        $s = $k->dohvatiSpecijalizaciju();

        if ($s->noviEmail == null) {
            return;
        }

        $hash = md5(microtime());

        DB::table("mejlverifikacija")->insert([
            "hash" => $hash,
            "Korisnik_idKor" => $k->idKor
        ]);

        Mail::to($s->noviEmail)->send(new VerifikacijaIzmena($hash));
    }

    /**
     * Iniciranje zahteva za zaposlenje
     *
     * @param string $email Mejl adresa na koju se šalje mejl zahteva za zaposlenje
     * @param ZaposlenjeModel $z Novokreirani zapis o zaposlenju koji nosi informacije potrebne za ovu verifikaciju
     *
     * @return void
     */
    public static function posaljiMejlZaposlenje(string $email, ZaposlenjeModel $z) {
        $hash = md5(microtime());

        DB::table("mejlverifikacija")->insert([
            "hash" => $hash,
            "Zaposlenje_idZaposlenja" => $z->idZaposlenja,
            "email" => $email
        ]);

        Mail::to($email)->send(new PotvrdaZaposlenja($z->biznis->zvanicnoIme, $hash));
    }
}
