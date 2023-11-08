<?php

// Autori: Mateja Milošević 2020/0487

namespace App\Models\Korisnici;

use App\Models\NeradnoVremeModel;
use App\Models\TerminModel;
use App\Models\Traits\MusterijaTrait;
use App\Models\ZaposlenjeModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * ZaposleniModel – klasa ORM modela Zaposleni tabele u bazi
 *
 * @version 1.0
 */
class ZaposleniModel extends Model
{
    use MusterijaTrait;

    protected $table = "zaposleni";
    protected $primaryKey = "Musterija_Korisnik_idKor";

    public $incrementing = false;
    public $timestamps = false;

    /**
     * Dohvatanje svih zapisa o zaposlenju vezanih za ovog zaposlenog
     *
     * @return HasMany
     */
    public function zaposlenja() {
        return $this->hasMany(ZaposlenjeModel::class, "Zaposleni_Musterija_Korisnik_idKor",
                                                                                    "Musterija_Korisnik_idKor");
    }

    /**
     * Dohvatanje svih biznisa u kojima je zaposlen ovaj zaposleni
     *
     * @return BiznisModel[]
     */
    public function biznisi() {
        // Nažalost, ne može kao Eloquent relacija bez menjanja baze

        $res = [];

        foreach ($this->zaposlenja as $zaposlenje) {
            $res[] = $zaposlenje->biznis;
        }

        return $res;
    }

    /**
     * Dohvatanje svih termina ovog zaposlenog
     *
     * @return HasMany
     */
    public function termini() {
        return $this->hasMany(TerminModel::class, "Zaposleni_Musterija_Korisnik_idKor",
                                                                                    "Musterija_Korisnik_idKor");
    }

    /**
     * Dohvatanje svih zapisa o neradnom vremenu vezanih za ovog zaposlenog
     *
     * @return HasMany
     */
    public function neradnoVreme() {
        return $this->hasMany(NeradnoVremeModel::class, "Zaposleni_Musterija_Korisnik_idKor",
                                                                                    "Musterija_Korisnik_idKor");
    }

    /**
     * Dohvatanje prosečne ocene ovog zaposlenog
     *
     * @return string
     */
    public function dohvatiOcenu() {
        $ocena = ZaposleniModel::withAvg("termini as prosecnaOcena", "ocenaKorisnika")
                        ->where("Musterija_Korisnik_idKor", $this->Musterija_Korisnik_idKor)->first()["prosecnaOcena"];

        if ($ocena == null) {
            $ocena = "N/A";
        } else {
            $ocena = number_format($ocena, 1);
        }

        return $ocena;
    }
}
