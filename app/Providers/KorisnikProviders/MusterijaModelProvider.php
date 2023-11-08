<?php

// Autori: Mateja Milošević 2020/0487

namespace App\Providers\KorisnikProviders;

use App\Models\Korisnici\MusterijaModel;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\UserProvider;

/**
 * Provajder autentifikacije za musterije (i zaposlene)
 *
 * @version 1.0
 */
class MusterijaModelProvider implements UserProvider {
    public function retrieveByToken($identifier, $token) {}
    public function updateRememberToken(Authenticatable $user, $token) {}

    public function retrieveById($identifier) {
        return MusterijaModel::find($identifier)->korisnik();
    }

    public function retrieveByCredentials(array $credentials) {
        foreach (MusterijaModel::all() as $m) {
            if ($m->email == $credentials["email"]) {
                return $m->korisnik();
            }
        }

        return null;
    }

    public function validateCredentials(Authenticatable $user, array $credentials) {
        return $user->getAuthPassword() == $credentials["lozinka"];
    }
}
