<?php

// Autori: Mateja Milošević 2020/0487
//         Željko Jazarević 2020/0484
//         Miloš Paunović 2018/0294

namespace App\Http\Controllers;

use App\Http\Controllers\Interfaces\AuthKontrolerInterfejs;
use App\Http\Controllers\Utility\Helperi;
use App\Http\Controllers\Utility\Kalendar\Kalendar;
use App\Models\Korisnici\BiznisModel;
use App\Models\Korisnici\KorisnikModel;
use App\Models\Korisnici\MusterijaModel;
use App\Models\Korisnici\ZaposleniModel;
use App\Models\NeradnoVremeModel;
use App\Models\TerminModel;
use App\Models\UslugaModel;
use App\Models\ZaposlenjeModel;
use DateTime;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

/**
* MusterijaController – kontroler zadužen za sve funkcionalnosti dostupne mušterijama i zaposlenima
*
* @version 1.0
*/
class MusterijaController extends Controller implements AuthKontrolerInterfejs {
    /**
     * Kreiranje nove instance
     *
     * @return void
     */
    function __construct() {
        $this->middleware("auth:musterija");
    }

    /**
     * Implementacija funkcije interfejsa
     *
     * @return View
     */
    public function glavnaStrana() {
        if (Helperi::dohvatiAuthKorisnika()->tipKorisnika == TIP_MUSTERIJA) {
            return $this->musterijaZakazi();
        } else {
            return $this->zaposleniMojKalendar();
        }
    }

    /**
     * Implementacija funkcije interfejsa
     *
     * @return View
     */
    public function profil() {
        if (session("customErr") != null) {
            return view("musterija.profil")->withErrors(session("customErr"));
        }

        return view("musterija.profil", [ "status" => session("status") ]);
    }

    /**
     * Vršenje akcije izmene podataka mušterije (ili zaposlenog)
     *
     * @param Request $request Zahtev koji se obrađuje
     *
     * @return RedirectResponse
     * @throws ValidationException
     */
    public function izmeniPodatke(Request $request) {
        $this->validate($request, [
            "ime" => "required|alpha",
            "prezime" => "required|alpha",
            "korisnickoIme" => "required|alpha|min:5|max:15",
            "email" => "required|email",
            "staraLozinka" => "nullable|required_with:novaLozinka",
            "novaLozinka" => "nullable|min:8|max:20",
            "novaLozinkaP" => "nullable|required_with:novaLozinka|min:8|max:20|same:novaLozinka"
        ], [
            "required" => "Polje je obavezno.",
            "alpha" => "Polje može sadržati samo alfabetske karaktere.",
            "min" => "Polje mora imati bar :min karaktera.",
            "max" => "Polje ne sme imati više od :max karaktera.",
            "email" => "Polje mora biti u formatu email adrese.",
            "same" => "Unosi za novu lozinku se ne poklapaju.",
            "required_with" => "Ukoliko želite da promenite lozinku, sva tri polja za lozinku moraju biti uneta."
        ]);

        $m = Helperi::dohvatiAuthKorisnika();

        if ($request->novaLozinka != null) {
            if ($m->lozinka != $request->staraLozinka) {
                return redirect()->back()->with([ "customErr" => [ "staraLozinka" => "Unesena lozinka nije tačna." ] ]);
            }

            if (strtolower($request->novaLozinka) == $request->novaLozinka) {
                return redirect()->back()->with([ "customErr" => [ "novaLozinka" => "Lozinka mora sadržati bar jedno veliko slovo." ] ]);
            }
        }

        if ($request->ime != $m->ime) {
            if (!ctype_upper(substr($request->ime, 0, 1))) {
                return redirect()->back()->with([ "customErr" => [ "ime" => "Ime mora da počinje velikim slovom." ] ]);
            }
        }

        if ($request->prezime != $m->prezime) {
            if (!ctype_upper(substr($request->prezime, 0, 1))) {
                return redirect()->back()->with([ "customErr" => [ "prezime" => "Prezime mora da počinje velikim slovom." ] ]);
            }
        }

        if ($request->ime != $m->ime) {
            $m->ime = $request->ime;
        }

        if ($request->prezime != $m->prezime) {
            $m->prezime = $request->prezime;
        }

        if ($request->korisnickoIme != $m->korisnickoIme) {
            $m->korisnickoIme = $request->korisnickoIme;
        }

        if ($request->novaLozinka != null) {
            $m->lozinka = $request->novaLozinka;
        }

        $status = 0;

        if ($request->email != $m->email) {
            $m->noviEmail = $request->email;
            $m->push();

            Helperi::posaljiMejlVerifikacijuIzmena(KorisnikModel::find($m->idKor));

            $status = 1;
        }

        $m->push();
        return redirect()->back()->with([ "status" => $status ]);
    }

    /**
     * Prikaz stranice na kojoj se nalaze podaci potrebni korisniku da zakaze zeljeni termin
     *
     * @param int $idBiznisa Biznis za koji se prikazuju podaci
     *
     * @return View
     */
    public function biznisInfo($idBiznisa){
        $biznis = BiznisModel::where("Korisnik_idKor", $idBiznisa)->first();
        return view("musterija.biznisInfo", ['biznis' => $biznis]);
    }

    /**
     * Prikaz stranice za zakazivanje termina
     *
     * @return View
     */
    public function musterijaZakazi(){
        $biznisi = BiznisModel::pretraga('', 'poOceni');

        return view('musterija.zakazi', ['biznisi' => $biznisi]);
    }

    /**
     * Prikaz stranice sa rezultatima pretrage
     *
     * @return View
     */
    public function musterijaPretraga(Request $request) {
        $imeBiznisa = $request['imeBiznisa'];
        $sortiraj = $request['sortiraj'];

        $biznisi = BiznisModel::pretraga($imeBiznisa, $sortiraj);

        return view('musterija.zakazi', ['biznisi' => $biznisi]);
    }

    /**
     * Prikaz stranice na kojoj se nalaze zakazani termini koji jos uvek nisu prosli
     *
     * @return View
     */
    public function musterijaZakazaniTermini(){
        $termini = TerminModel::where("Musterija_Korisnik_idKor", auth()->user()->idKor)->where("vremePocetka", ">=", now())->get();
        //$termini = TerminModel::all();
        return view('musterija.zakazaniTermini', ['termini' => $termini]);
    }

    public function musterijaOtkaziTermin($idTermina){
        //odkomentarisati posle, da ne bih birsao redove, pa ih dodavao dok testiram
        TerminModel::where("idTermina", $idTermina)->delete();
        return $this->musterijaZakazaniTermini();
    }

    /**
     * Prikaz stranice na kojoj se nalaze odradjeni termini koji su prosli
     *
     * @return View
     */
    public function musterijaOdradjeniTermini(){
        $termini = TerminModel::where("Musterija_Korisnik_idKor", auth()->user()->idKor)->where("prikaziKorisniku", 1)->where("vremeKraja", "<", now())->get();
        return view('musterija.odradjeniTermini', ['termini' => $termini]);
    }

    /**
     * Korisnik je odbijo da da ocenu i ostavi komentar, nece mu se vise prikazivati u listi odradjenih termina
     *
     * @return View
     */
    public function nePrikazujKorisniku($idTermina){
        TerminModel::where("idTermina", $idTermina)->update([
            'prikaziKorisniku' => 0
        ]);
        $termini = TerminModel::where("Musterija_Korisnik_idKor", auth()->user()->idKor)->where("prikaziKorisniku", 1)->where("vremeKraja", "<", now())->get();
        return redirect()->route('odradjeniTermini', ['termini' => $termini]);
    }

    /**
     * Korisnik unosi ocenu i komentar za odredjeni termin
     *
     * @return View
     */
    public function musterijaOceniTermin(Request $request, $idTermina){
        $termini = TerminModel::where("idTermina", $idTermina)->update([
            'ocenaKorisnika' => $request->ocena,
            'komentarKorisnika' => $request->komentar,
            'prikaziKorisniku' => 0
        ]);
        $termini = TerminModel::where("Musterija_Korisnik_idKor", auth()->user()->idKor)->where("prikaziKorisniku", 1)->where("vremeKraja", "<", now())->get();
        return redirect()->route('odradjeniTermini', ['termini' => $termini]);
    }

    /**
     * Prikaz stranice na kojoj se nalazi kalendar zaposlenog
     *
     * @return View
     */
    public function zaposleniMojKalendar(){
        return view('zaposleni.mojKalendar');
    }

    /**
     * Destinaciona ruta prilikom prihvatanja zahteva za zaposlenje
     *
     * @param Request $request Zahtev koji se obrađuje
     *
     * @return View
     */
    public function prihvatiZaposlenje(Request $request) {
        $q = DB::table("mejlverifikacija")->where("hash", $request->hash);

        $red = $q->get();

        if ($red->isEmpty()) {
            abort(404);
        }

        $red = $red[0];

        if ($red->Zaposlenje_idZaposlenja != null && $red->email != null) {
            $z = ZaposlenjeModel::find($red->Zaposlenje_idZaposlenja);

            $k = KorisnikModel::where("email", $red->email)->first();

            if (Helperi::dohvatiAuthKorisnika()->idKor != $k->idKor) {
                abort(404);
            }

            // Prihvatanje zahteva za zaposlenje
            if ($z->zaposlen == 0 && $z->zaposleni == null && ZaposleniModel::find($k->idKor) == null) {
                $zap = new ZaposleniModel();
                $zap->Musterija_Korisnik_idKor = $k->idKor;

                $zap->tipKorisnika = TIP_ZAPOSLENI;

                $zap->push();

                $z->zaposleni()->associate($zap);

                $z->prihvacenZahtev = date("Y-m-d H:i:s");
                $z->zaposlen = 1;

                $z->push();

                $q->delete();

                session([ "tipKorisnika" => TIP_ZAPOSLENI ]);

                return view("porukaRedirect", [
                    "naslov" => "Zahtev za Zaposlenje",
                    "poruka" => "Uspešno prihvaćen zahtev za zaposlenje!"
                ]);
            }
        }

        abort(404);
    }

    /**
     * GET ruta koja vraća podatke u JSON formatu za renderovanje kalendara na stranici Moj Kalendar zaposlenog, ili
     * alternativno, podatke za prikaz legende na istoj stranici
     *
     * @param Request|null $request Zahtev koji se obrađuje
     * @param bool $legenda Da li funkcija treba da vrati podatke za prikaz legende
     *
     * @return array|JsonResponse
     */
    public function kalendarZaposleni(Request|null $request, bool $legenda = false) {
        $zap = Helperi::dohvatiAuthKorisnika();

        $ulazi = [];

        $boje = [ "#0d6efd", "#6610f2", "#6f42c1", "#d63384", "#dc3545", "#fd7e14", "#198754", "#20c9970", "#0dcaf0" ];

        $idx = 0;

        if ($legenda) {
            $legenda = [];

            foreach ($zap->biznisi() as $biznis) {
                $legenda[$biznis->Korisnik_idKor] = $boje[$idx];
                $idx = ($idx + 1) % 9;
            }

            return $legenda;
        }

        foreach ($zap->biznisi() as $biznis) {
            $zaBiznis = Kalendar::dohvatiUlazePrikaz($biznis, $zap, strtotime($request->start), strtotime($request->end));

            foreach ($zaBiznis as $ulaz) {
                $ulaz->backgroundColor = $boje[$idx];
            }

            $ulazi = array_merge($ulazi, $zaBiznis);

            $idx = ($idx + 1) % 9;
        }

        return response()->json($ulazi);
    }

    public function odaberiUslugu($idBiznisa, $idZaposleni, $idUsluga){
        $biznis = BiznisModel::where("Korisnik_idKor", $idBiznisa)->first();
        $izabranZaposleni = ZaposleniModel::where("Musterija_Korisnik_idKor", $idZaposleni)->first();
        $usluge = UslugaModel::where("Biznis_Korisnik_idKor", $idBiznisa)->where("Zaposleni_Musterija_Korisnik_idKor", $idZaposleni)->get();
        $izabranaUsluga = UslugaModel::where("idCenovnik", $idUsluga)->first();
        return view("musterija.biznisInfo", [
            'biznis' => $biznis,
            'izabranZaposleni' => $izabranZaposleni,
            'usluge' => $usluge,
            'izabranaUsluga' => $izabranaUsluga
        ]);
    }

    public function odaberiZaposlenog($idBiznisa, $idZaposleni){
        $biznis = BiznisModel::where("Korisnik_idKor", $idBiznisa)->first();
        $izabranZaposleni = ZaposleniModel::where("Musterija_Korisnik_idKor", $idZaposleni)->first();
        $usluge = UslugaModel::where("Biznis_Korisnik_idKor", $idBiznisa)->where("Zaposleni_Musterija_Korisnik_idKor", $idZaposleni)->get();
        return view("musterija.biznisInfo", [
            'biznis' => $biznis,
            'izabranZaposleni' => $izabranZaposleni,
            'usluge' => $usluge
        ]);
    }

    /**
     * GET ruta koja vraća podatke u JSON formatu za prikaz zauzetih termina kalendara za funkcije musterije
     *
     * @param Request $request Zahtev koji se obrađuje
     *
     * @return JsonResponse
     */
    public function kalendarZaposlenog(Request $request) {
        $b = BiznisModel::where('Korisnik_idKor', $request->biznis)->first();
        $zap = ZaposleniModel::find($request->zap);

        $ulazi = Kalendar::dohvatiUlazePrikaz($b, $zap, strtotime($request->start), strtotime($request->end));

        $m = Helperi::dohvatiAuthKorisnika();
        $imePrezime = $m->ime." ".$m->prezime;

        foreach ($ulazi as $ulaz) {
            if (array_key_exists("musterija", $ulaz->extendedProps) && $ulaz->extendedProps["musterija"] != $imePrezime) {
                $ulaz->title = "Zauzeto";
                $ulaz->backgroundColor = "#dc3545";
            }
        }

        return response()->json($ulazi);
    }


    public function zakaziTermin(Request $request){

        $vremePocetka = $request->vremePocetka;
        $vremeKraja = new DateTime($vremePocetka);
        $vremeKraja = $vremeKraja->modify('+' . $request->trajanje . ' minutes');

        $b = BiznisModel::find($request->idBiznisa);
        $zap = ZaposleniModel::find($request->idZapsoleni);

        if (Kalendar::preklapanje($b, $zap, strtotime($vremePocetka), $vremeKraja->getTimestamp())) {
            abort(404);
        }

        TerminModel::insert([
            'Biznis_Korisnik_idKor' => $request->idBiznisa,
            'Zaposleni_Musterija_Korisnik_idKor' => $request->idZapsoleni,
            'Musterija_Korisnik_idKor' => $request->idMusterija,
            'vremePocetka' => $vremePocetka,
            'vremeKraja' => $vremeKraja,
            'trajanje' => $request->trajanje,
            'ocenaKorisnika' => null,
            'komentarKorisnika' => null,
            'ocenaBiznisa' => null,
            'komentarBiznisa' => null,
            'prikaziKorisniku' => 1,
            'prikaziBiznisu' => 1
        ]);

        return response()->noContent();
    }

    public function recenzijeBiznisa($idBiznisa){
        $biznis = BiznisModel::where("Korisnik_idKor", $idBiznisa)->first();
        $recenzije = TerminModel::where("Biznis_Korisnik_idKor", $idBiznisa)->where("ocenaKorisnika", "!=", null)->get();
        return view("musterija.biznisInfo", ['biznis' => $biznis, 'recenzije' => $recenzije]);
    }
}
