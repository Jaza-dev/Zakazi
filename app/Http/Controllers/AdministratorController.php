<?php

// Autori: Željko Jazarević 2020/0484
//         Mateja Milošević 2020/0487

namespace App\Http\Controllers;

use App\Http\Controllers\Interfaces\AuthKontrolerInterfejs;
use App\Models\Korisnici\BiznisModel;
use App\Models\Korisnici\KorisnikModel;
use App\Models\Korisnici\MusterijaModel;
use App\Models\TipBiznisaModel;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;

/**
 * AdministratorController – kontroler zadužen za sve funkcionalnosti dostupne administratorima
 *
 * Verifikacija biznisa, pravljenje novih tipova biznisa, izmena podataka korisnika
 *
 * @version 1.0
 */
class AdministratorController extends Controller implements AuthKontrolerInterfejs {
    /**
     * Kreiranje nove instance
     *
     * @return void
     */
    function __construct() {
        $this->middleware("auth:admin");
    }

    /**
     * Implementacija funkcije interfejsa
     *
     * @return View
     */
    public function glavnaStrana() {
        return $this->verifikacijaBiznisa();
    }

    /**
     * Implementacija funkcije interfejsa
     *
     * @return void
     */
    public function profil() {
        abort(404);
    }

    /**
     * Prikazivanje stranice na kojoj se vrsi verifikacija biznisa
     *
     * @return View
     */
    public function verifikacijaBiznisa() {
        $neVerifikovani = BiznisModel::where('verifikovan', 0)->orWhere('potvrdioIzmene', 1)->orWhere('novoZvanicnoIme', "!=", null)->where('noviEmail', null)->get();
        $tipoviBiznisa = TipBiznisaModel::all();

        return view('admin.verifikacija', ['neVerifikovani' => $neVerifikovani, 'tipoviBiznisa' => $tipoviBiznisa]);
    }

    /**
     * Prikazivanje stranice na kojoj admin moze da menja podatke svih korisnika sistema
     *
     * @return View
     */
    public function izmenaPodataka() {
        return view('admin.izmenaPodataka');
    }

    /**
     * Dohvatanje Korisnika na osnovu zadatog e-maila
     *
     * @param Request $request Zahtev koji se obrađuje
     *
     * @return View
     */
    public function prikaziPodatkeKorisnika(Request $request) {
        $korisnik = KorisnikModel::where("email", "like", $request->email)->first();

        if ($korisnik == null){
            session(["status" => 2]);
            return view('admin.izmenaPodataka');
        }

        return view('admin.izmenaPodataka', ['korisnik' => $korisnik->dohvatiSpecijalizaciju()]);
    }

    /**
     * Radi izmenu svih tipova korisnika, kada se proslede podaci
     *
     * @param Request $request Zahtev koji se obrađuje
     *
     * @return View
     */
    public function izmeniPodatke(Request $request) {
        $korisnik = KorisnikModel::find($request->idKor)->dohvatiSpecijalizaciju();

        KorisnikModel::where('idKor', $request->idKor)->update([
            'email' => $request->email
        ]);

        if ($korisnik->tipKorisnika == 1 || $korisnik->tipKorisnika == 3) {
            MusterijaModel::where('Korisnik_idKor', $request->idKor)->update([
                'korisnickoIme' => $request->korisnickoIme,
                'ime' => $request->ime,
                'prezime' => $request->prezime
            ]);
        } else if ($korisnik->tipKorisnika == 2) {
            BiznisModel::where('Korisnik_idKor', $request->idKor)->update([
                'zvanicnoIme' => $request->zvanicnoIme,
                'brojTelefona' => $request->brojTelefona,
                'imeVlasnika' => $request->imeVlasnika,
                'prezimeVlasnika' => $request->prezimeVlasnika,
                'PIB' => $request->PIB
            ]);
        }
        session(["status" => 1]);
        $korisnik = KorisnikModel::find($request->idKor)->dohvatiSpecijalizaciju();
        return view('admin.izmenaPodataka', ['korisnik' => $korisnik]);
    }

    /**
     * Kreira novi tip biznisa
     *
     * @param Request $request Zahtev koji se obrađuje
     *
     * @return RedirectResponse
     */
    public function dodajNoviTipBiznisa(Request $request) {
        TipBiznisaModel::insert([
            'naziv' => $request->imeTipaBiznisa
        ]);

        return redirect()->route('index');
    }

    /**
     * Radi verifikaciju biznisa. Postoje dve vrste verifikacije.
     * Prva je ako biznis prvi put se registruje, onda mu amdin dodeljuje tip biznisa
     * Druga je ako biznis menja ime ili email. Onda admin ponovo mora da verifikuje biznis.
     *
     * @param Request $request Zahtev koji se obrađuje
     * @param int $idKor Query parametar koji označava ID biznisa koji se verifikuje
     *
     * @return RedirectResponse
     */
    public function verifikuj(Request $request, $idKor) {
        $biznis = BiznisModel::find($idKor);
        $tipBiznisa = TipBiznisaModel::where('naziv', $request->tipBiznisa)->first();

        if ($biznis->verifikovan == 0 && $tipBiznisa->idTipBiznisa != $biznis->tipBiznisa->idTipBiznisa) {
            // tip biznisa koji je korisnik uneo i onaj koji je admin dodelio se razlikuju
            // sada je potrebno obrisati onaj tip koji je korisnik uneo, i dodeliti onaj koji je admin odabrao

            $stariIdTipBiznisa = $biznis->tipBiznisa->idTipBiznisa;

            $biznis->tipBiznisa()->associate($tipBiznisa);
            $biznis->push();

            TipBiznisaModel::where('idTipBiznisa', $stariIdTipBiznisa)->delete();
        }

        BiznisModel::where('Korisnik_idKor', $idKor)->update([
            'verifikovan' => 1,
            'potvrdioIzmene' => 0
        ]);

        $noviEmail = $biznis->noviEmail;
        if ($noviEmail != null) {
            KorisnikModel::where('idKor', $idKor)->update([
                'email' => $noviEmail
            ]);

            BiznisModel::where('Korisnik_idKor', $idKor)->update([
                'noviEmail' => null
            ]);
        }

        $novoZvanicnoIme = $biznis->novoZvanicnoIme;
        if ($novoZvanicnoIme != null) {
            BiznisModel::where('Korisnik_idKor', $idKor)->update([
                'novoZvanicnoIme' => null,
                'zvanicnoIme' => $novoZvanicnoIme
            ]);
        }

        return redirect()->route('index');
    }

    /**
     * Radi odbijanje verifikacije, gde se verifikovan postavlja na -1,
     * tada se biznis vise ne pojavljuje u listi za verifikaciju
     *
     * @param int $idKor Query parametar koji označava ID biznisa čija se verifikacija odbija
     *
     * @return RedirectResponse
     */
    public function odbij($idKor){
        $biznis = BiznisModel::find($idKor);

        if ($biznis->verifikovan == 0) {
            // ako pre toga nije bio verifikovan znaci da je dodao novi tip biznisa
            // potrebno je obrisati tip biznisa koji je on dodao pri registraciji

            //nephodno je da se postavi tip biznisa na neku vrednost kako bi se novi uneti tip obrisao
            $prviTipBiznisa = TipBiznisaModel::first();
            BiznisModel::where('Korisnik_idKor', $idKor)->update([
                'TipBiznisa_idTipBiznisa' => $prviTipBiznisa->idTipBiznisa,
                'verifikovan' => -1
            ]);
            TipBiznisaModel::where('idTipBiznisa', $biznis->tipBiznisa->idTipBiznisa)->delete();

            
        }else{
            //prethodno je biznis bio verifikovan, ali novi podaci nisu zadovoljavajuci
            BiznisModel::where('Korisnik_idKor', $idKor)->update([
                'noviEmail' => null,
                'novoZvanicnoIme' => null,
                'potvrdioIzmene' => 0
            ]);
        }

        return redirect()->route('index');
    }
}
