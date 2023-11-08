<?php

namespace App\Providers;

use App\Models\Korisnici\AdministratorModel;
use App\Models\Korisnici\BiznisModel;
use App\Models\Korisnici\KorisnikModel;
use App\Models\Korisnici\MusterijaModel;
use App\Providers\KorisnikProviders\AdministratorModelProvider;
use App\Providers\KorisnikProviders\BiznisModelProvider;
use App\Providers\KorisnikProviders\KorisnikModelProvider;
use App\Providers\KorisnikProviders\MusterijaModelProvider;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Auth;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        //
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        Auth::provider("eloquent", function(Application $app, array $config) {
            return match ($config["model"]) {
                KorisnikModel::class => new KorisnikModelProvider(),
                MusterijaModel::class => new MusterijaModelProvider(),
                BiznisModel::class => new BiznisModelProvider(),
                AdministratorModel::class => new AdministratorModelProvider(),
                default => throw new Exception()
            };
        });
    }
}
