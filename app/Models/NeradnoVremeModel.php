<?php

// Autori: Mateja Milošević 2020/0487

namespace App\Models;

use App\Models\Korisnici\BiznisModel;
use App\Models\Korisnici\MusterijaModel;
use App\Models\Korisnici\ZaposleniModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * NeradnoVremeModel – klasa ORM modela NeradnoVreme tabele u bazi
 *
 * @version 1.0
 */
class NeradnoVremeModel extends Model
{
    protected $table = "neradnovreme";
    protected $primaryKey = "idNeradnoVreme";

    protected $fillable = [
        "vremePocetka",
        "vremeKraja",
        "trajanje"
    ];

    public $timestamps = false;

    /**
     * Dohvatanje biznisa na koji se odnosi ovaj zapis
     *
     * @return BelongsTo
     */
    public function biznis() {
        return $this->belongsTo(BiznisModel::class, "Biznis_Korisnik_idKor", "Korisnik_idKor");
    }

    /**
     * Dohvatanje zaposlenog na kog se odnosi ovaj zapis
     *
     * @return BelongsTo
     */
    public function zaposleni() {
        return $this->belongsTo(ZaposleniModel::class, "Zaposleni_Musterija_Korisnik_idKor",
                                                                                    "Musterija_Korisnik_idKor");
    }
}
