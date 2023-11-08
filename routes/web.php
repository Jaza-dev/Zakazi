<?php

// Autori: Željko Jazarević 2020/0484
//         Mateja Milošević 2020/0487
//         Radosav Popadić 2020/0056
//         Miloš Paunović 2018/0294

use App\Http\Controllers\AdministratorController;
use App\Http\Controllers\BazaController;
use App\Http\Controllers\GostController;
use App\Http\Controllers\BiznisController;
use App\Http\Controllers\MusterijaController;
use App\Http\Controllers\SviController;
use App\Http\Controllers\Utility\Helperi;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// APSOLUTNO SVI

Route::get('/verifikacijaIzmena/{hash}', [SviController::class, 'verifikacijaIzmena'])->name('verifikacijaIzmena');

// NEAUTENTIFIKOVANI KORISNICI

Route::get('/registracijaKorisnika', [GostController::class, 'registracijaKorisnika'])->name('registracijaKorisnika');
Route::post('/registracijaKorisnika', [GostController::class, 'registracijaKorisnikaAkcija'])->name('registracijaKorisnikaAkcija');

Route::get('/registracijaBiznisa', [GostController::class, 'registracijaBiznisa'])->name('registracijaBiznisa');
Route::post('/registracijaBiznisa', [GostController::class, 'registracijaBiznisaAkcija'])->name('registracijaBiznisaAkcija');

Route::get('/prijava', [GostController::class, 'prijavaForma'])->name('prijavaForma');
Route::post('/prijava', [GostController::class, 'prijavaAkcija'])->name('prijavaAkcija');

Route::get('/posaljiMejl/{id}', [GostController::class, 'posaljiMejl'])->name('posaljiMejl');

Route::get('/mejlVerifikacija/{hash}', [GostController::class, 'mejlVerifikacija'])->name('mejlVerifikacija');

// SVI AUTENTIFIKOVANI

Route::get('/', [BazaController::class, 'index'])->name('index');

Route::get('/profil', [BazaController::class, 'profil'])->name('profil');

Route::get('/odjava', [BazaController::class, 'odjava'])->name('odjava');

// MUSTERIJE

Route::get('/prihvatiZaposlenje/{hash}', [MusterijaController::class, 'prihvatiZaposlenje'])->name('prihvatiZaposlenje');

Route::post('/izmeniPodatkeM', [MusterijaController::class, 'izmeniPodatke'])->name('izmeniPodatkeMusterija');

Route::get('/zakazi', [MusterijaController::class, 'musterijaZakazi'])->name('musterijaZakazi');

Route::get('/biznisInfo/{idBiznisa}', [MusterijaController::class, 'biznisInfo'])->name('biznisInfo');

Route::get('/zakazaniTermini', [MusterijaController::class, 'musterijaZakazaniTermini'])->name('zakazaniTermini');
Route::get('/odradjeniTermini', [MusterijaController::class, 'musterijaOdradjeniTermini'])->name('odradjeniTermini');

Route::get('/musterijaOceniTermin/{idTermina}', [MusterijaController::class, 'musterijaOceniTermin'])->name('musterijaOceniTermin');
Route::get('/nePrikazujKorisniku/{idTermina}', [MusterijaController::class, 'nePrikazujKorisniku'])->name('nePrikazujKorisniku');

Route::get('/musterijaOtkaziTermin/{idTermina}', [MusterijaController::class, 'musterijaOtkaziTermin'])->name('musterijaOtkaziTermin');

Route::get('pretraga', [MusterijaController::class, 'musterijaPretraga'])->name('musterijaPretraga');

Route::get('/odaberiZaposlenog/{idBiznisa}/{idZaposleni}', [MusterijaController::class, 'odaberiZaposlenog'])->name('odaberiZaposlenog');
Route::get('/odaberiUslugu/{idBiznisa}/{idZaposleni}/{idUsluga}', [MusterijaController::class, 'odaberiUslugu'])->name('odaberiUslugu');

Route::get('/data/kalendarZaposlenog', [MusterijaController::class, 'kalendarZaposlenog'])->name('kalendarZaposlenog');
Route::get('/recenzijeBiznisa/{idBiznisa}', [MusterijaController::class, 'recenzijeBiznisa'])->name('recenzijeBiznisa');

Route::get('/zakaziTermin', [MusterijaController::class, 'zakaziTermin'])->name('zakaziTermin');

// ZAPOSLENI

Route::get('/mojKalendar', [MusterijaController::class, 'zaposleniMojKalendar'])->name('zaposleniMojKalendar');

Route::get('/data/kalendarZaposleni', [MusterijaController::class, 'kalendarZaposleni'])->name('kalendarZaposleni');

// BIZNISI

Route::get('/mojiZaposleni', [BiznisController::class, 'mojiZaposleni'])->name('mojiZaposleni');
Route::get('/zavrseniTermini', [BiznisController::class, 'zavrseniTermini'])->name('zavrseniTermini');

Route::get('/data/zavrseniTerminiBiznis', [BiznisController::class, 'dataZavrseniTerminiBiznis'])->name('dataZavrseniTerminiBiznis');
Route::get('/data/biznisOceniTermin/{idTermina}', [BiznisController::class, 'biznisOceniTermin'])->name('biznisOceniTermin');
Route::get('/data/nePrikazujBiznisu/{idTermina}', [BiznisController::class, 'nePrikazujBiznisu'])->name('nePrikazujBiznisu');

Route::post('/zahtevZaposlenje', [BiznisController::class, 'zahtevZaposlenje'])->name('zahtevZaposlenje');

Route::post('/izmeniPodatkeB', [BiznisController::class, 'izmeniPodatke'])->name('izmeniPodatkeBiznis');

Route::get('/data/kalendarBiznis', [BiznisController::class, 'kalendarData'])->name('kalendarBiznis');

Route::post('/data/dodajNeradnoVreme', [BiznisController::class, 'dodajNeradnoVreme'])->name('dodajNeradnoVreme');
Route::get('/data/obrisiNeradnoVreme', [BiznisController::class, 'obrisiNeradnoVreme'])->name('obrisiNeradnoVreme');

Route::get('/data/otkaziTermin', [BiznisController::class, 'otkaziTermin'])->name('otkaziTermin');

Route::get('data/uslugeZaposlenog/{idZaposleni}', [BiznisController::class, 'uslugeZaposlenog'])->name('uslugeZaposlenog');
Route::get('data/izmeniUslugu/{idZaposleni}', [BiznisController::class, 'izmeniUslugu'])->name('izmeniUslugu');
Route::get('data/izbrisiUslugu/{idZaposleni}', [BiznisController::class, 'izbrisiUslugu'])->name('izbrisiUslugu');
Route::get('data/dodajUslugu/{idZaposleni}', [BiznisController::class, 'dodajUslugu'])->name('dodajUslugu');

// ADMINISTRATORI

Route::get('/verifikacijaBiznisa', [AdministratorController::class, 'verifikacijaBiznisa'])->name('verifikacijaBiznisa');

Route::post('/dodajNoviTipBiznisa', [AdministratorController::class, 'dodajNoviTipBiznisa'])->name('dodajNoviTipBiznisa');

Route::post('/verifikuj/{idKor}', [AdministratorController::class, 'verifikuj'])->name('verifikuj');
Route::get('/odbij/{idKor}', [AdministratorController::class, 'odbij'])->name('odbij');

Route::post('/prikaziPodatkeKorisnika', [AdministratorController::class, 'prikaziPodatkeKorisnika'])->name('prikaziPodatkeKorisnika');

Route::get('/izmenaPodataka', [AdministratorController::class, 'izmenaPodataka'])->name('izmenaPodataka');
Route::post('/izmeniPodatke', [AdministratorController::class, 'izmeniPodatke'])->name('izmeniPodatke');

// FALLBACK

Route::fallback(function() {
    if (Helperi::dohvatiAuthKorisnika() == null) {
        return redirect("prijava");
    } else {
        abort(404);
    }
});
