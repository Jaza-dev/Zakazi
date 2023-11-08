<?php

// Autori: Mateja Milošević 2020/0487

namespace App\Http\Controllers\Interfaces;

use Illuminate\Contracts\View\View;

/**
 * Interfejs koji obećava funkcije koje dele svi (ne-vendor) kontroleri aplikacije zaduženi za autorizovane korisnike
 *
 * @version 1.0
 */
interface AuthKontrolerInterfejs
{
    /**
     * Prikazivanje glavne stranice kontrolera koji implementira interfejs KontrolerInterfejs
     *
     * @return View
     */
    public function glavnaStrana();

    /**
     * Prikazivanje profilne stranice kontrolera koji implementira interfejs KontrolerInterfejs
     *
     * @return View
     */
    public function profil();
}
