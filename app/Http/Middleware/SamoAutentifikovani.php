<?php

// Autori: Mateja Milošević 2020/0487

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware koji dozvoljava pristup ruti/kontroleru ukoliko je korisnik trenutno autentifikovan
 * kao bilo koji tip korisnika
 *
 * @version 1.0
 */
class SamoAutentifikovani {
    public function handle(Request $request, Closure $next, string ...$guards): Response {
        $guards = empty($guards) ? [ "musterija", "biznis", "admin" ] : $guards;

        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                return $next($request);
            }
        }

        $redirect = $request->path();

        if ($redirect == "/") {
            $redirect = null;
        }

        return Redirect::route("prijavaForma", [ "redirect" => $redirect ]);
    }
}
