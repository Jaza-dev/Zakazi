<?php

// Autori: Mateja Milošević 2020/0487

namespace App\Models\Traits;

use App\Models\Korisnici\KorisnikModel;

/**
 * KorisnikTrait – pomoćni trait za pristup atributima Korisnika kroz specijalizacije Korisnika
 *
 * @version 1.0
 */
trait KorisnikTrait
{
    /**
     * @var KorisnikModel $korisnik Instanca generalne klase Korisnik vezana za ovu instancu specijalizacije
     */
    private $korisnik = null;

    /**
     * Dohvatanje instance generalne klase Korisnik vezane za ovu instancu specijalizacije
     *
     * @return KorisnikModel
     */
    public function korisnik() {
        if ($this->korisnik == null) {
            $this->korisnik = KorisnikModel::find($this->Korisnik_idKor);
        }

        return $this->korisnik;
    }

    /**
     * Redefinicija magične funkcije kojom se dohvataju atributi iz instance generalne klase Korisnik
     *
     * @param string $key Ime atributa
     *
     * @return mixed
     */
    public function __get($key) {
        return parent::__get($key) ?? $this->korisnik()->$key;
    }

    /**
     * Redefinicija magične funkcije kojom se postavljaju vrednosti atributa iz instance generalne klase Korisnik
     *
     * @param string $key Ime atributa
     * @param mixed $value Nova vrednost atributa
     *
     * @return void
     */
    public function __set($key, $value) {
        if (in_array($key, array_merge($this->getFillable(), $this->getGuarded(), [ $this->primaryKey ]))) {
            parent::__set($key, $value);

            if ($key == $this->primaryKey) {
                $this->korisnik = null;
                $this->korisnik();
            }
        } else {
            $this->korisnik()->$key = $value;
        }
    }

    /**
     * Redefinicija funkcije za čuvanje ove instance specijalizacije u bazu, koja takođe automatski čuva instancu
     * generalne klase Korisnik
     *
     * @param array $options Opcije pri čuvanju
     *
     * @return bool
     */
    public function save(array $options = []) {
        return parent::save($options) && $this->korisnik()->save($options);
    }

    /**
     * Redefinicija funkcije za čuvanje ove instance specijalizacije u bazu, koja takođe automatski čuva instancu
     * generalne klase Korisnik (i sve vezane relacije)
     *
     * @return bool
     */
    public function push() {
        return parent::push() && $this->korisnik()->push();
    }

    public function getAuthPassword() {
        return $this->korisnik()->getAuthPassword();
    }
}
