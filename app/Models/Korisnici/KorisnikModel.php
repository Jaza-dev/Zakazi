<?php

// Autori: Mateja Milošević 2020/0487

namespace App\Models\Korisnici;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

define("TIP_ADMIN", 0);
define("TIP_MUSTERIJA", 1);
define("TIP_BIZNIS", 2);
define("TIP_ZAPOSLENI", 3);

/**
 * KorisnikModel – klasa ORM modela Korisnik tabele u bazi
 *
 * @version 1.0
 */
class KorisnikModel extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = "korisnik";
    protected $primaryKey = "idKor";

    protected $fillable = [
        "email",
        "lozinka",
        "tipKorisnika"
    ];

    protected $guarded = [
        "aktivan"
    ];

    public $timestamps = false;

    /**
     * Dohvatanje Eloquent objekta specijalizovanog tipa za ovog korisnika
     *
     * @return AdministratorModel|BiznisModel|MusterijaModel|ZaposleniModel|null
     */
    public function dohvatiSpecijalizaciju() {
        return match ($this->tipKorisnika) {
            TIP_ADMIN => AdministratorModel::find($this->idKor),
            TIP_BIZNIS => BiznisModel::find($this->idKor),
            TIP_MUSTERIJA => MusterijaModel::find($this->idKor),
            TIP_ZAPOSLENI => ZaposleniModel::find($this->idKor),
            default => null
        };
    }

    public function getAuthPassword() {
        return $this->lozinka;
    }
}
