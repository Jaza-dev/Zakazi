<?php

// Autori: Mateja Milošević 2020/0487

namespace App\Http\Controllers;

use App\Http\Controllers\Interfaces\AuthKontrolerInterfejs;
use App\Http\Controllers\Utility\Helperi;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

/**
* BazaController – kontroler zadužen za sve funkcionalnosti dostupne svim autentifikovanim korisnicima
*
* @version 1.0
*/
class BazaController extends Controller {
    /**
     * Kreiranje nove instance
     * 
     * @return void
     */
    function __construct() {
        $this->middleware("svi_autentifikovani");
    }

    /**
     * Dohvatanje kontrolera zaduženog za tip korisnika trenutno autentifikovanog korisnika
     *
     * @return AuthKontrolerInterfejs
     */
    private function dohvatiAuthKontroler() {
        $kor = Helperi::dohvatiAuthKorisnika();

        if ($kor == null) {
            return app(GostController::class);
        }

        return match ($kor->tipKorisnika) {
            TIP_ADMIN => app(AdministratorController::class),
            TIP_MUSTERIJA, TIP_ZAPOSLENI => app(MusterijaController::class),
            TIP_BIZNIS => app(BiznisController::class),
            default => null
        };
    }

    /**
     * Dohvatanje glavne strane za određeni tip korisnika
     *
     * @return View
     */
    public function index() {
        return $this->dohvatiAuthKontroler()->glavnaStrana();
    }

    /**
     * Dohvatanje profilne strane za određeni tip korisnika
     *
     * @return View
     */
    public function profil() {
        return $this->dohvatiAuthKontroler()->profil();
    }

    /**
     * Vršenje odjave trenutno prijavljenog korisnika
     *
     * @return RedirectResponse
     */
    public function odjava(Request $request) {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route("prijavaForma");
    }
}
