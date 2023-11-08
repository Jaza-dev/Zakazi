<?php

// Autori: Mateja Milošević 2020/0487

namespace App\Providers\KorisnikProviders;

use App\Models\Korisnici\KorisnikModel;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\UserProvider;

/**
 * Provajder autentifikacije za sve korisnike
 *
 * @version 1.0
 */
class KorisnikModelProvider implements UserProvider {
    public function retrieveByToken($identifier, $token) {}
    public function updateRememberToken(Authenticatable $user, $token) {}

    public function retrieveById($identifier) {
        return KorisnikModel::find($identifier);
    }

    public function retrieveByCredentials(array $credentials) {
        foreach (KorisnikModel::all() as $k) {
            if ($k->email == $credentials["email"]) {
                return $k;
            }
        }

        return null;
    }

    public function validateCredentials(Authenticatable $user, array $credentials) {
        return $user->getAuthPassword() == $credentials["lozinka"];
    }
}
