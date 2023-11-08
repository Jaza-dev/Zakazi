<?php

// Autori: Mateja Milošević 2020/0487

namespace App\Providers\KorisnikProviders;

use App\Models\Korisnici\AdministratorModel;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\UserProvider;

/**
 * Provajder autentifikacije za administratore
 *
 * @version 1.0
 */
class AdministratorModelProvider implements UserProvider {
    public function retrieveByToken($identifier, $token) {}
    public function updateRememberToken(Authenticatable $user, $token) {}

    public function retrieveById($identifier) {
        return AdministratorModel::find($identifier)->korisnik();
    }

    public function retrieveByCredentials(array $credentials) {
        foreach (AdministratorModel::all() as $a) {
            if ($a->email == $credentials["email"]) {
                return $a->korisnik();
            }
        }

        return null;
    }

    public function validateCredentials(Authenticatable $user, array $credentials) {
        return $user->getAuthPassword() == $credentials["lozinka"];
    }
}
