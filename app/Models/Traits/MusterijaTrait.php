<?php

// Autori: Mateja Milošević 2020/0487

namespace App\Models\Traits;

use App\Models\Korisnici\MusterijaModel;

/**
 * MusterijaTrait – pomoćni trait za pristup atributima Musterije kroz specijalizacije Musterije
 *
 * @version 1.0
 */
trait MusterijaTrait
{
    /**
     * @var MusterijaModel $musterija Instanca generalne klase Musterija vezana za ovu instancu specijalizacije
     */
    private $musterija = null;

    /**
     * Dohvatanje instance generalne klase Musterija vezane za ovu instancu specijalizacije
     *
     * @return MusterijaModel
     */
    public function musterija() {
        if ($this->musterija == null) {
            $this->musterija = MusterijaModel::find($this->Musterija_Korisnik_idKor);
        }

        return $this->musterija;
    }

    /**
     * Redefinicija magične funkcije kojom se dohvataju atributi iz instance generalne klase Musterija
     *
     * @param string $key Ime atributa
     *
     * @return mixed
     */
    public function __get($key) {
        return parent::__get($key) ?? $this->musterija()->$key;
    }

    /**
     * Redefinicija magične funkcije kojom se postavljaju vrednosti atributa iz instance generalne klase Musterija
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
                $this->musterija = null;
                $this->musterija();
            }
        } else {
            $this->musterija()->$key = $value;
        }
    }

    /**
     * Redefinicija funkcije za čuvanje ove instance specijalizacije u bazu, koja takođe automatski čuva instancu
     * generalne klase Musterija
     *
     * @param array $options Opcije pri čuvanju
     *
     * @return bool
     */
    public function save(array $options = []) {
        return parent::save($options) && $this->musterija()->save($options);
    }

    /**
     * Redefinicija funkcije za čuvanje ove instance specijalizacije u bazu, koja takođe automatski čuva instancu
     * generalne klase Musterija (i sve vezane relacije)
     *
     * @return bool
     */
    public function push() {
        return parent::push() && $this->musterija()->push();
    }

    public function getAuthPassword() {
        return $this->musterija()->getAuthPassword();
    }
}
