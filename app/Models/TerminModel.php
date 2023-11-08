<?php

// Autori: Mateja Milošević 2020/0487

namespace App\Models;

use App\Models\Korisnici\BiznisModel;
use App\Models\Korisnici\MusterijaModel;
use App\Models\Korisnici\ZaposleniModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * TerminModel – klasa ORM modela Termin tabele u bazi
 *
 * @version 1.0
 */
class TerminModel extends Model
{
    protected $table = "termin";
    protected $primaryKey = "idTermina";

    protected $fillable = [
        "vremePocetka",
        "vremeKraja",
        "trajanje",
        "ocenaKorisnika",
        "komentarKorisnika",
        "ocenaBiznisa",
        "komentarBiznisa",
        "prikaziKorisniku",
        "prikaziBiznisu"
    ];

    public $timestamps = false;

    /**
     * Dohvatanje biznisa na koji se odnosi ovaj termin
     *
     * @return BelongsTo
     */
    public function biznis() {
        return $this->belongsTo(BiznisModel::class, "Biznis_Korisnik_idKor", "Korisnik_idKor");
    }

    /**
     * Dohvatanje zaposlenog na kog se odnosi ovaj termin
     *
     * @return BelongsTo
     */
    public function zaposleni() {
        return $this->belongsTo(ZaposleniModel::class, "Zaposleni_Musterija_Korisnik_idKor",
                                                                                    "Musterija_Korisnik_idKor");
    }

    /**
     * Dohvatanje musterije na koju se odnosi ovaj termin
     *
     * @return BelongsTo
     */
    public function musterija() {
        return $this->belongsTo(MusterijaModel::class, "Musterija_Korisnik_idKor", "Korisnik_idKor");
    }
}
