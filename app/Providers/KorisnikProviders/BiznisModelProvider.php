<?php

// Autori: Mateja Milošević 2020/0487

namespace App\Providers\KorisnikProviders;

use App\Models\Korisnici\BiznisModel;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\UserProvider;

/**
 * Provajder autentifikacije za biznise
 *
 * @version 1.0
 */
class BiznisModelProvider implements UserProvider {
    public function retrieveByToken($identifier, $token) {}
    public function updateRememberToken(Authenticatable $user, $token) {}

    public function retrieveById($identifier) {
        return BiznisModel::find($identifier)->korisnik();
    }

    public function retrieveByCredentials(array $credentials) {
        foreach (BiznisModel::all() as $b) {
            if ($b->email == $credentials["email"]) {
                return $b->korisnik();
            }
        }

        return null;
    }

    public function validateCredentials(Authenticatable $user, array $credentials) {
        return $user->getAuthPassword() == $credentials["lozinka"];
    }
}
