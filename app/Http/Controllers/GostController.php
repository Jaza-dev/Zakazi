<?php

// Autori: Željko Jazarević 2020/0484
//         Mateja Milošević 2020/0487
//         Radosav Popadic 2020/0056

namespace App\Http\Controllers;

use App\Models\Korisnici\BiznisModel;
use App\Models\Korisnici\KorisnikModel;
use App\Models\Korisnici\MusterijaModel;
use App\Models\TipBiznisaModel;
use App\Http\Controllers\Utility\Helperi;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

/**
* GostController – kontroler zadužen za sve funkcionalnosti dostupne neautentifikovanim korisnicima
*
* @version 1.0
*/
class GostController extends Controller {
    /**
     * Kreiranje nove instance
     *
     * @return void
     */
    function __construct() {
        $this->middleware("guest");
    }

    /**
     * Prikazivanje forme za registraciju korisnika
     *
     * @return View
     */
    public function registracijaKorisnika() {
        return view('musterija.registracija');
    }

    /**
     * Vršenje akcije registracije korisnika
     *
     * @param Request $request Zahtev koji se obrađuje
     *
     * @return View
     *
     * @throws ValidationException
     */
    public function registracijaKorisnikaAkcija(Request $request) {
        $this->validate($request, [
            'korisnickoIme' => 'required|between:5,15|alpha:ascii',
            'email' => 'required|email',
            'lozinka' => 'required|between:8,20|regex:/.*[A-Z].*/',
            'ponovljenaLozinka' => 'required|same:lozinka',
            'ime' => 'required|alpha|regex:/^[A-ZČĆŽŠĐ].*/',
            'prezime' => 'required|alpha|regex:/^[A-ZČĆŽŠĐ].*/'
        ], [
            'korisnickoIme.between' => 'Korisničko ime mora imati izmedju :min i :max karaktera.',
            'lozinka.between' => 'Lozinka mora imati izmedju :min i :max karaktera.',
            'lozinka.regex' => 'Lozinka mora sadržati bar jedno veliko slovo.',
            'ime.regex' => 'Ime mora počinjati velikim slovom.',
            'prezime.regex' => 'Prezime mora počinjati velikim slovom.',
            'ponovljenaLozinka.same' => 'Polje se mora poklapati sa lozinkom.',
            'alpha' => 'Polje mora da sadrži samo alfabetske karaktere.'
        ]);

        if(KorisnikModel::where('email', $request->email)->first()!=null){
            return back()->withInput()->with("status", "Već postoji nalog sa ovim email-om.");
        }
        $korisnik=new KorisnikModel([
            'email' => $request->email,
            'lozinka' => $request->lozinka,
            'tipKorisnika' => TIP_MUSTERIJA,
        ]);
        $korisnik->aktivan=0;
        $korisnik->push();

        $musterija=new MusterijaModel([
            'korisnickoIme' => $request->korisnickoIme,
            'ime' => $request->ime,
            'prezime' => $request->prezime
        ]);
        $musterija->Korisnik_idKor = $korisnik->idKor;
        $musterija->push();


        Helperi::posaljiMejlVerifikacijuReg($korisnik);

        return view("verifikacijaObavestenje", ['email' => $request->email, 'id' => $korisnik->idKor]);
    }

    /**
     * Prikazivanje forme za registraciju biznisa
     *
     * @return View
     */
    public function registracijaBiznisa() {
        return view('biznis.registracija');
    }

    /**
     * Vršenje akcije registracije biznisa
     *
     * @param Request $request Zahtev koji se obrađuje
     *
     * @return View
     *
     * @throws ValidationException
     */
    public function registracijaBiznisaAkcija(Request $request) {
        $this->validate($request, [
            'zvanicnoIme' => 'required|regex:/^[a-zA-Z\s]+$/',
            'email' => 'required|email',
            'lozinka' => 'required|between:8,20|regex:/.*[A-Z].*/',
            'ponovljenaLozinka' => 'required|same:lozinka',
            'imeVlasnika' => 'required|alpha|regex:/^[A-ZČĆŽŠĐ].*/',
            'prezimeVlasnika' => 'required|alpha|regex:/^[A-ZČĆŽŠĐ].*/',
            'tipBiznisa' => 'required|regex:/^[a-zA-Z\s]+$/',
            'telefon' => 'required',
            'opis' => 'required',
            'pib' => 'nullable|size:8|regex:/^[1-9][0-9]{6}[1-9]$/'
        ], [
            'lozinka.between' => 'Lozinka mora imati izmedju :min i :max karaktera.',
            'lozinka.regex' => 'Lozinka mora sadržati bar jedno veliko slovo.',
            'imeVlasnika.regex' => 'Ime mora počinjati velikim slovom.',
            'prezimeVlasnika.regex' => 'Prezime mora počinjati velikim slovom.',
            'ponovljenaLozinka.same' => 'Polje se mora poklapati sa lozinkom.',
            'alpha' => 'Polje mora da sadrži samo alfabetske karaktere.',
            'zvanicnoIme.regex' => 'Polje mora da sadrži samo alfabetske karaktere.',
            'tipBiznisa.regex' => 'Polje mora da sadrži samo alfabetske karaktere.',
            'pib' => 'PIB mora biti broj izmedju 10000001 i 99999999.'
        ]);
        if(KorisnikModel::where('email', $request->email)->first()!=null){
            return back()->withInput()->with("status", "Već postoji nalog sa ovim email-om.");
        }
        if(BiznisModel::where('zvanicnoIme', $request->zvanicnoIme)->first()!=null){
            return back()->withInput()->with("status", "Već postoji biznis sa ovim imenom.");
        }

        $korisnik=new KorisnikModel([
            'email' => $request->email,
            'lozinka' => $request->lozinka,
            'tipKorisnika' => TIP_BIZNIS,
            'aktivan' => 0
        ]);
        $korisnik->aktivan=0;
        $korisnik->push();

        $tip=new TipBiznisaModel([
            'naziv' => $request->tipBiznisa
        ]);
        $tip->push();

        $biznis=new BiznisModel([
            'zvanicnoIme' => $request->zvanicnoIme,
            'brojTelefona' => $request->telefon,
            'imeVlasnika' => $request->imeVlasnika,
            'prezimeVlasnika' => $request->prezimeVlasnika,
            'PIB' => $request->pib,
            'opis' => $request->opis,
            'verifikovan' => 0
        ]);
        $biznis->verifikovan=0;
        $biznis->Korisnik_idKor = $korisnik->idKor;
        $biznis->tipBiznisa()->associate($tip);
        $biznis->push();

        Helperi::posaljiMejlVerifikacijuReg($korisnik);

        return view("verifikacijaObavestenje", ['email' => $request->email, 'id' => $korisnik->idKor]);
    }

    /**
     * Vrsenje akcije ponovnog slanja mejla
     *
     * @param Request $request Zahtev koji se obrađuje
     *
     * @return RedirectResponse
     */
    public function posaljiMejl(Request $request){
        $korisnik=KorisnikModel::find($request->id);
        if($korisnik==null || $korisnik->aktivan==1)
            abort(400);
        Helperi::posaljiMejlVerifikacijuReg($korisnik);
        return view("verifikacijaObavestenje", ['email' => $korisnik->email, 'id' => $korisnik->idKor]);
    }

    /**
     * Prikazivanje forme za prijavu na sistem i biznisa i korisnika
     *
     * @param Request $request Zahtev koji se obrađuje
     *
     * @return View
     */
    public function prijavaForma(Request $request) {
        return view('prijava', [ "redirect" => $request->redirect ]);
    }

    /**
     * Vršenje akcije prijave na sistem
     *
     * @param Request $request Zahtev koji se obrađuje
     *
     * @return RedirectResponse
     */
    public function prijavaAkcija(Request $request) {
        $korisnik = KorisnikModel::where('email', $request->email)->where('lozinka', $request->lozinka)->first();

        if ($korisnik != null) {
            if ($korisnik->aktivan == 0) {
                return back()->withInput()->with("status", "Niste verifikovali email.")->with("link", $korisnik->idKor);
            }

            if ($korisnik->tipKorisnika == TIP_BIZNIS) {
                $verifikovan = $korisnik->dohvatiSpecijalizaciju()->verifikovan;

                if ($verifikovan != 1) {
                    $msg = "Greška!";

                    if ($verifikovan == 0) {
                        $msg = "Vaš biznis nije verifikovan od strane administratora. Molimo pokušajte kasnije.";
                    } else if ($verifikovan == -1) {
                        $msg = "Vaša registracija biznisa je odbijena od strane administratora.";
                    }

                    return back()->withInput()->with("status", $msg);
                }
            }
        } else {
            return back()->withInput()->with("status", "Niste dobro uneli email ili lozinku.");
        }

        $guard = match ($korisnik->tipKorisnika) {
            TIP_ADMIN => "admin",
            TIP_MUSTERIJA, TIP_ZAPOSLENI => "musterija",
            TIP_BIZNIS => "biznis"
        };

        if (auth($guard)->attempt($request->only("email", "lozinka"))) {
            session([ "tipKorisnika" => Auth::guard($guard)->user()->tipKorisnika ]);
            session([ "authGuard" => $guard ]);

            if ($request->redirect) {
                return redirect($request->redirect);
            } else {
                return redirect()->route("index");
            }
        }

        return back()->withInput()->with("status", "Niste dobro uneli email ili lozinku.");
    }

    /**
     * Destinaciona ruta prilikom verifikacije mejla pri registraciji
     *
     * @param Request $request Zahtev koji se obrađuje
     *
     * @return View|RedirectResponse
     */
    public function mejlVerifikacija(Request $request) {
        $q = DB::table("mejlverifikacija")->where("hash", $request->hash);

        $red = $q->get();

        if ($red->isEmpty()) {
            return redirect("prijava");
        }

        $red = $red[0];

        if ($red->Korisnik_idKor != null) {
            $k = KorisnikModel::find($red->Korisnik_idKor);

            // Verifikacija mejla pri registraciji
            if ($k->aktivan == 0) {
                $k->aktivan = 1;
                $k->push();

                $q->delete();

                return view("porukaRedirect", [
                    "naslov" => "Verifikacija Mejl Adrese",
                    "poruka" => "Uspešno potvrđena mejl adresa!"
                ]);
            }
        }

        return redirect("prijava");
    }
}
