<?php

// Autori: Mateja Milošević 2020/0487

namespace App\Models\Korisnici;

use App\Models\Traits\KorisnikTrait;
use Illuminate\Foundation\Auth\User as Authenticatable;

/**
 * AdministratorModel – klasa ORM modela Administrator tabele u bazi
 *
 * @version 1.0
 */
class AdministratorModel extends Authenticatable
{
    use KorisnikTrait;

    protected $table = "administrator";
    protected $primaryKey = "Korisnik_idKor";

    public $timestamps = false;
}
