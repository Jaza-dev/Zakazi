<?php

// Autori: Mateja Milošević 2020/0487

namespace App\Models\Korisnici;

use App\Models\TerminModel;
use App\Models\Traits\KorisnikTrait;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;

/**
 * MusterijaModel – klasa ORM modela Musterija tabele u bazi
 *
 * @version 1.0
 */
class MusterijaModel extends Authenticatable
{
    use KorisnikTrait;

    protected $table = "musterija";
    protected $primaryKey = "Korisnik_idKor";

    protected $fillable = [
        "korisnickoIme",
        "ime",
        "prezime"
    ];

    protected $guarded = [
        "noviEmail"
    ];

    public $incrementing = false;
    public $timestamps = false;

    /**
     * Dohvatanje svih termina ove mušterije
     *
     * @return HasMany
     */
    public function termini() {
        return $this->hasMany(TerminModel::class, "Musterija_Korisnik_idKor", "Korisnik_idKor");
    }

    /**
     * Dohvatanje prosečne ocene ove mušterije
     *
     * @return string
     */
    public function dohvatiOcenu() {
        $ocena = MusterijaModel::withAvg("termini as prosecnaOcena", "ocenaBiznisa")
                                    ->where("Korisnik_idKor", $this->Korisnik_idKor)->first()["prosecnaOcena"];

        if ($ocena == null) {
            $ocena = "N/A";
        } else {
            $ocena = number_format($ocena, 1);
        }

        return $ocena;
    }
}
