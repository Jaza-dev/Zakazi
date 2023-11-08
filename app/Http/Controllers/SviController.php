<?php

// Autori: Mateja Milošević 2020/0487

namespace App\Http\Controllers;

use App\Http\Controllers\Utility\Helperi;
use App\Models\Korisnici\KorisnikModel;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

/**
 * SviController – kontroler zadužen za sve funkcionalnosti dostupne svim (i autentifikovanim i neautentifikovanim)
 * korisnicima
 *
 * @version 1.0
 */
class SviController extends Controller {
    /**
     * Destinaciona ruta prilikom verifikacije izmene podataka
     *
     * @param Request $request Zahtev koji se obrađuje
     *
     * @return View|RedirectResponse
     */
    public function verifikacijaIzmena(Request $request) {
        $q = DB::table("mejlverifikacija")->where("hash", $request->hash);

        $red = $q->get();

        $auth = Helperi::dohvatiAuthKorisnika();

        if ($red->isEmpty()) {
            if ($auth == null) {
                return redirect("prijava");
            }

            abort(404);
        }

        $red = $red[0];
        if ($red->Korisnik_idKor != null && ($auth == null || $auth->idKor == $red->Korisnik_idKor)) {
            $k = KorisnikModel::find($red->Korisnik_idKor);

            // Verifikacija promene mejla prilikom izmene podataka
            if ($k->aktivan == 1) {
                $s = $k->dohvatiSpecijalizaciju();

                if ($s->noviEmail == null) {
                    if ($auth == null) {
                        return redirect("prijava");
                    }

                    abort(404);
                }

                if ($k->tipKorisnika == TIP_MUSTERIJA || $k->tipKorisnika == TIP_ZAPOSLENI) {
                    $s->email = $s->noviEmail;
                    $s->noviEmail = null;
                } else if ($k->tipKorisnika == TIP_BIZNIS) {
                    $s->potvrdioIzmene = 1;
                }

                $s->push();

                $q->delete();

                return view("porukaRedirect", [
                    "naslov" => "Potvrda Izmene Podataka",
                    "poruka" => "Uspešno potvrđene izmene podataka!"
                ]);
            }
        }

        if ($auth == null) {
            return redirect("prijava");
        }

        abort(404);
    }
}
