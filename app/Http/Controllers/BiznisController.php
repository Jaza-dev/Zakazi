<?php

// Autori: Željko Jazarević 2020/0484
//         Mateja Milošević 2020/0487
//         Miloš Paunović 2018/0294
//         Radosav Popadić 2020/0056

namespace App\Http\Controllers;

use App\Http\Controllers\Utility\Helperi;
use App\Http\Controllers\Utility\Kalendar\Kalendar;
use App\Mail\ObavestenjeOtkazivanje;
use App\Models\Korisnici\BiznisModel;
use App\Models\Korisnici\KorisnikModel;
use App\Models\Korisnici\ZaposleniModel;
use App\Models\NeradnoVremeModel;
use App\Models\TerminModel;
use App\Models\UslugaModel;
use App\Models\ZaposlenjeModel;
use Illuminate\Contracts\View\View;
use App\Http\Controllers\Interfaces\AuthKontrolerInterfejs;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

/**
* BiznisController – kontroler zadužen za sve funkcionalnosti dostupne biznisima
*
* @version 1.0
*/
class BiznisController extends Controller implements AuthKontrolerInterfejs {
    /**
     * Kreiranje nove instance
     *
     * @return void
     */
    function __construct() {
        $this->middleware("auth:biznis");
    }

    /**
     * Implementacija funkcije interfejsa
     *
     * @return View
     */
    public function glavnaStrana() {
        return $this->mojiZaposleni(null);
    }

    /**
     * Implementacija funkcije interfejsa
     *
     * @return View
     */
    public function profil() {
        if (session("customErr") != null) {
            return view("biznis.profil")->withErrors(session("customErr"));
        }

        return view("biznis.profil", [ "status" => session("status") ]);
    }

    /**
     * Vršenje akcije izmene podataka biznisa
     *
     * @param Request $request Zahtev koji se obrađuje
     *
     * @return RedirectResponse
     * @throws ValidationException
     */
    public function izmeniPodatke(Request $request){
        $this->validate($request, [
            "imeVlasnika" => "required|alpha",
            "prezimeVlasnika" => "required|alpha",
            "brojTelefona" => "required",
            "zvanicnoIme" => "required|regex:/^[a-zA-Z\s]+$/", //|min:5|max:15",
            "opis" => "required",
            "email" => "required|email",
            "staraLozinka" => "nullable|required_with:novaLozinka",
            "novaLozinka" => "nullable|min:8|max:20",
            "novaLozinkaP" => "nullable|required_with:novaLozinka|min:8|max:20|same:novaLozinka",
            'PIB' => 'nullable|size:8|regex:/^[1-9][0-9]{6}[1-9]$/'
        ], [
            "required" => "Polje je obavezno.",
            "alpha" => "Polje može sadržati samo alfabetske karaktere.",
            "zvanicnoIme.regex" => "Polje može sadržati samo alfabetske karaktere.",
            "min" => "Polje mora imati bar :min karaktera.",
            "max" => "Polje ne sme imati više od :max karaktera.",
            "email" => "Polje mora biti u formatu email adrese.",
            "same" => "Unosi za novu lozinku se ne poklapaju.",
            "required_with" => "Ukoliko želite da promenite lozinku, sva tri polja za lozinku moraju biti uneta.",
            'PIB' => 'PIB mora biti broj izmedju 10000001 i 99999999.'
        ]);

        $b = Helperi::dohvatiAuthKorisnika();

        if ($request->email != $b->email){
            if(KorisnikModel::where('email', $request->email)->first()!=null){
                return redirect()->back()->with([ "customErr" => [ "email" => "Već postoji nalog sa ovim email-om." ] ]);
            }
        }

        if ($request->novaLozinka != null) {
            if ($b->lozinka != $request->staraLozinka) {
                return redirect()->back()->with([ "customErr" => [ "staraLozinka" => "Unesena lozinka nije tačna." ] ]);
            }

            if (strtolower($request->novaLozinka) == $request->novaLozinka) {
                return redirect()->back()->with([ "customErr" => [ "novaLozinka" => "Lozinka mora sadržati bar jedno veliko slovo." ] ]);
            }
        }

        if ($request->imeVlasnika != $b->imeVlasnika) {
            if (!ctype_upper(substr($request->imeVlasnika, 0, 1))) {
                return redirect()->back()->with([ "customErr" => [ "imeVlasnika" => "Ime mora da počinje velikim slovom." ] ]);
            }
        }

        if ($request->prezimeVlasnika != $b->prezimeVlasnika) {
            if (!ctype_upper(substr($request->prezimeVlasnika, 0, 1))) {
                return redirect()->back()->with([ "customErr" => [ "prezimeVlasnika" => "Prezime mora da počinje velikim slovom." ] ]);
            }
        }

        if ($request->imeVlasnika != $b->imeVlasnika) {
            $b->imeVlasnika = $request->imeVlasnika;
        }

        if ($request->prezimeVlasnika != $b->prezimeVlasnika) {
            $b->prezimeVlasnika = $request->prezimeVlasnika;
        }

        if ($request->PIB != $b->PIB) {
            $b->PIB = $request->PIB;
        }

        if ($request->opis != $b->opis) {
            $b->opis = $request->opis;
        }

        if ($request->brojTelefona != $b->brojTelefona) {
            $b->brojTelefona = $request->brojTelefona;
        }

        if ($request->novaLozinka != null) {
            $b->lozinka = $request->novaLozinka;
        }

        $status = 0;

        if ($request->email != $b->email) {
            $b->noviEmail = $request->email;
            $b->push();

            $status += 1;
            Helperi::posaljiMejlVerifikacijuIzmena(KorisnikModel::find($b->idKor));
        }

        if ($request->zvanicnoIme != $b->zvanicnoIme) {
            $b->novoZvanicnoIme = $request->zvanicnoIme;
            $b->push();

            $status += 2;
        }

        $b->push();

        return redirect()->back()->with([ "status" => $status ]);
    }

    /**
     * Prikaz stranice na kojoj se nalaze podaci o zaposlenima i funkcije vezane za zaposlene za odgovarajući biznis
     *
     * @param Request|null $request Zahtev koji se obrađuje
     *
     * @return View
     */
    public function mojiZaposleni(Request|null $request) {
        $b = Helperi::dohvatiAuthKorisnika();

        $params = [ "zaposleni" => $b->zaposleni() ];

        if ($request != null) {
            if ($request->has("zap")) {
                $params["izabran"] = ZaposleniModel::find($request->zap);
            }
        }

        if (session("status") !== null) {
            $params["status"] = session("status");
        }

        return view("biznis.mojiZaposleni", $params);
    }

    /**
     * Vršenje procedure slanja zahteva za zaposlenje
     *
     * @param Request $request Zahtev koji se obrađuje
     *
     * @return RedirectResponse
     */
    public function zahtevZaposlenje(Request $request) {
        $validator = Validator::make($request->all(), [
            "email" => "required|email:rfc",
        ]);

        if ($validator->fails()) {
            return redirect()->back()->with([ "status" => 1 ]);
        }

        $b = Helperi::dohvatiAuthKorisnika();

        $zap = new ZaposlenjeModel();

        $zap->biznis()->associate($b);

        $zap->poslatZahtev = date("Y-m-d H:i:s");
        $zap->zaposlen = 0;

        $zap->push();

        Helperi::posaljiMejlZaposlenje($request->email, $zap);

        return redirect()->back()->with([ "status" => 0 ]);
    }

    /**
     * Prikaz stranice na kojoj se nalaze završeni termini spremni za ocenjivanje
     *
     * @return View
     */
    public function zavrseniTermini() {
        return view("biznis.zavrseniTermini");
    }


    /**
     * GET ruta koja vraća podatke o svim završenim terminima u JSON formatu
     *
     * @return JsonResponse
     */
    public function dataZavrseniTerminiBiznis() {
        $termini = TerminModel::where("Biznis_Korisnik_idKor", auth()->user()->idKor)->where("prikaziBiznisu", 1)->where("vremeKraja", "<", now())->orderBy("vremeKraja")->get();

        // treba u Utility ili da ima funkcija u TerminModel
        $terminiNiz = [];
        foreach($termini as $termin) {
            $terminiNiz[] = [
                "idTermina" => $termin->idTermina,
                "musterija" => $termin->musterija->ime.' '.$termin->musterija->prezime,
                "zaposleni" => $termin->zaposleni->ime.' '.$termin->zaposleni->prezime,
                "vremePocetka" => $termin->vremePocetka,
                "vremeKraja" => $termin->vremeKraja,
                "rutaOceni" => route('biznisOceniTermin', ['idTermina' => $termin->idTermina]),
                "rutaNePrikazuj" => route('nePrikazujBiznisu', ['idTermina' => $termin->idTermina])
            ];
        }

        return response()->json($terminiNiz);
    }

    /**
     * Ocenjivanje termina parametrima prosleđenim zahtevom
     * Namenjeno za potrebe AJAX poziva (ne vraća nikakav prikaz)
     *
     * @param Request $request Zahtev koji se obrađuje
     * @param int $idTermina id termina koji se obrađuje
     *
     * @return Response
     */
    public function biznisOceniTermin(Request $request, int $idTermina) {
        TerminModel::where("idTermina", $idTermina)->update([
            'ocenaBiznisa' => $request->ocena,
            'komentarBiznisa' => $request->komentar,
            'prikaziBiznisu' => 0
        ]);

        return response()->noContent();
    }

    /**
     * Uklanjanje termina iz pregleda završenih termina
     * Namenjeno za potrebe AJAX poziva (ne vraća nikakav prikaz)
     *
     * @param int $idTermina id termina koji se obrađuje
     *
     * @return Response
     */
    public function nePrikazujBiznisu(int $idTermina){
        TerminModel::where("idTermina", $idTermina)->update([
            'prikaziBiznisu' => 0
        ]);
        return response()->noContent();
    }


    /**
     * GET ruta koja vraća podatke u JSON formatu za renderovanje kalendara za funkcije biznisa
     *
     * @param Request $request Zahtev koji se obrađuje
     *
     * @return JsonResponse
     */
    public function kalendarData(Request $request) {
        $b = Helperi::dohvatiAuthKorisnika();
        $zap = ZaposleniModel::find($request->zap);

        return response()->json(Kalendar::dohvatiUlazePrikaz($b, $zap, strtotime($request->start), strtotime($request->end)));
    }

    /**
     * Vršenje akcije otkazivanja termina i slanja obaveštenja mušteriji
     * Namenjeno za potrebe AJAX poziva (ne vraća nikakav prikaz)
     *
     * @param Request $request Zahtev koji se obrađuje
     *
     * @return Response
     */
    public function otkaziTermin(Request $request) {
        $b = Helperi::dohvatiAuthKorisnika();

        $termin = TerminModel::find($request->idTermin);

        if ($termin != null && $termin->biznis->idKor == $b->idKor) {
            $zap = $termin->zaposleni->ime." ".$termin->zaposleni->prezime;

            $vremeOd = date("H:i d.m.Y.", strtotime($termin->vremePocetka));
            $vremeDo = date("H:i d.m.Y.", strtotime($termin->vremeKraja));

            Mail::to($termin->musterija->email)->send(new ObavestenjeOtkazivanje(
                $b->zvanicnoIme,
                $zap,
                "od ".$vremeOd." do ".$vremeDo
            ));

            $termin->delete();

            return response()->noContent();
        }

        abort(404);
    }

    /**
     * Vršenje akcije dodavanja zapisa o neradnom vremenu za određenog zaposlenog datog biznisa
     * Namenjeno za potrebe AJAX poziva (ne vraća nikakav prikaz)
     *
     * @param Request $request Zahtev koji se obrađuje
     *
     * @return Response
     */
    public function dodajNeradnoVreme(Request $request) {
        $b = Helperi::dohvatiAuthKorisnika();
        $zap = ZaposleniModel::find($request->zap);

        if ($request->pocetak % 600 != 0 || $request->kraj % 600 != 0 || $request->pocetak + 600 > $request->kraj) {
            abort(400);
        }

        if (Kalendar::preklapanje($b, $zap, $request->pocetak, $request->kraj)) {
            abort(400);
        }

        if ($zap != null && !empty(array_filter($b->zaposleni(), function($z) use ($zap) {
                return $z->idKor == $zap->idKor;
            }))) {
            Kalendar::dodajNeradnoVreme($b, $zap, $request->pocetak, $request->kraj);

            return response()->noContent();
        }

        abort(302);
    }

    /**
     * Vršenje akcije brisanja određenog zapisa o neradnom vremenu za određenog zaposlenog datog biznisa
     * Namenjeno za potrebe AJAX poziva (ne vraća nikakav prikaz)
     *
     * @param Request $request Zahtev koji se obrađuje
     *
     * @return Response
     */
    public function obrisiNeradnoVreme(Request $request) {
        $b = Helperi::dohvatiAuthKorisnika();

        $nVreme = NeradnoVremeModel::find($request->idNVreme);

        if ($nVreme != null && $nVreme->biznis->idKor == $b->idKor) {
            $nVreme->delete();

            return response()->noContent();
        }

        abort(404);
    }




    /**
     * GET ruta koja vraća sve usluge jednog zaposlenog u JSON formatu
     *
     * @param int $idZaposleni id zaposlenog koji pruža usluge
     *
     * @return JsonResponse
     */
    public function uslugeZaposlenog($idZaposleni) {
        $idBiznisa = auth()->user()->idKor;
        $usluge = UslugaModel::where("Biznis_Korisnik_idKor", $idBiznisa)->where("Zaposleni_Musterija_Korisnik_idKor", $idZaposleni)->get();

        $uslugeNiz = [];
        foreach($usluge as $usluga) {
            $uslugeNiz[] = [
                "id" => $usluga->idCenovnik,
                "naziv" => $usluga->nazivUsluge,
                "cena" => $usluga->cena,
                "trajanje" => $usluga->trajanje
            ];
        }


        return response()->json($uslugeNiz);
    }

    /**
     * Vršenje akcije izmene podataka usluge određene u zahtevu
     * Namenjeno za potrebe AJAX poziva (ne vraća nikakav prikaz)
     *
     * @param Request $request zahtev koji se obrađuje
     * @param int $idZaposleni id zaposlenog koji pruža uslugu
     *
     * @return JsonResponse
     */
    public function izmeniUslugu(Request $request, $idZaposleni) {
        $idBiznisa = auth()->user()->idKor;
        $usluge = UslugaModel::where("Biznis_Korisnik_idKor", $idBiznisa)
                             ->where("Zaposleni_Musterija_Korisnik_idKor", $idZaposleni)
                             ->where("idCenovnik", $request->id)
                             ->update([
                                "nazivUsluge" => $request->naziv ?: '',
                                "cena" => $request->cena ?: 0,
                                "trajanje" => $request->trajanje ?: 0
                             ]);

        return response()->noContent();
    }

    /**
     * Vršenje akcije brisanja usluge određene u zahtevu
     * Namenjeno za potrebe AJAX poziva (ne vraća nikakav prikaz)
     *
     * @param Request $request zahtev koji se obrađuje
     * @param int $idZaposleni id zaposlenog koji pruža uslugu
     *
     * @return JsonResponse
     */
    public function izbrisiUslugu(Request $request, $idZaposleni) {
        $idBiznisa = auth()->user()->idKor;
        $usluge = UslugaModel::where("Biznis_Korisnik_idKor", $idBiznisa)
                             ->where("Zaposleni_Musterija_Korisnik_idKor", $idZaposleni)
                             ->where("idCenovnik", $request->id)
                             ->delete();

        return response()->noContent();
    }

    /**
     * Vršenje akcije dodavanja nove usluge
     * Namenjeno za potrebe AJAX poziva (ne vraća nikakav prikaz)
     *
     * @param Request $request zahtev koji se obrađuje
     * @param int $idZaposleni id zaposlenog koji pruža uslugu
     *
     * @return JsonResponse
     */
    public function dodajUslugu(Request $request, $idZaposleni) {
        $idBiznisa = auth()->user()->idKor;

        $usluga = new UslugaModel();

        $usluga->biznis()->associate(BiznisModel::find($idBiznisa));
        $usluga->zaposleni()->associate(ZaposleniModel::find($idZaposleni));
        $usluga->nazivUsluge = $request->naziv ?: '';
        $usluga->cena = $request->cena ?: 0;
        $usluga->trajanje = $request->trajanje ?: 0;

        $usluga->push();

        return response()->noContent();
    }
}
