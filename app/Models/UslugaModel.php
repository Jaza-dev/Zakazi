<?php

// Autori: Mateja Milošević 2020/0487

namespace App\Models;

use App\Models\Korisnici\BiznisModel;
use App\Models\Korisnici\MusterijaModel;
use App\Models\Korisnici\ZaposleniModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * UslugaModel – klasa ORM modela Usluga tabele u bazi
 *
 * @version 1.0
 */
class UslugaModel extends Model
{
    protected $table = "usluga";
    protected $primaryKey = "idCenovnik";

    protected $fillable = [
        "nazivUsluge",
        "cena",
        "trajanje"
    ];

    public $timestamps = false;

    /**
     * Dohvatanje biznisa u kojem se nudi ova usluga
     *
     * @return BelongsTo
     */
    public function biznis() {
        return $this->belongsTo(BiznisModel::class, "Biznis_Korisnik_idKor", "Korisnik_idKor");
    }

    /**
     * Dohvatanje zaposlenog koji nudi ovu uslugu
     *
     * @return BelongsTo
     */
    public function zaposleni() {
        return $this->belongsTo(ZaposleniModel::class, "Zaposleni_Musterija_Korisnik_idKor",
                                                                                    "Musterija_Korisnik_idKor");
    }
}
