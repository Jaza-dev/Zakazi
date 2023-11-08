<?php

namespace Tests\Feature\app\Http\Controllers;

use App\Models\Korisnici\BiznisModel;
use App\Models\Korisnici\KorisnikModel;
use App\Models\TerminModel;
use App\Models\UslugaModel;
use App\Models\NeradnoVremeModel;
use App\Models\ZaposlenjeModel;
use Database\Seeders\KorisniciSeeder;
use Database\Seeders\TerminiSeeder;
use Database\Seeders\UslugeSeeder;
use Database\Seeders\ZaposlenjeSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * BiznisControllerTest – klasa koja sadrži testove BiznisController-a
 *
 * @version 1.0
 */
class BiznisControllerTest extends TestCase {
    use RefreshDatabase;

    // PRISTUP

    /**
     * Testiranje ograničavanja pristupa
     *
     * @return void
     */
    public function testPristup() {
        $this->seed([
            KorisniciSeeder::class,
            TerminiSeeder::class
        ]);

        // GOSTI

        $response = $this->get("/mojiZaposleni");
        $response->assertRedirectToRoute("prijavaForma", [ "redirect" => "mojiZaposleni" ]);

        $response = $this->get("/zavrseniTermini");
        $response->assertRedirectToRoute("prijavaForma", [ "redirect" => "zavrseniTermini" ]);

        $response = $this->get("/data/zavrseniTerminiBiznis");
        $response->assertRedirectToRoute("prijavaForma", [ "redirect" => "data/zavrseniTerminiBiznis" ]);

        $response = $this->get("/data/biznisOceniTermin/6");
        $response->assertRedirectToRoute("prijavaForma", [ "redirect" => "data/biznisOceniTermin/6" ]);

        $response = $this->get("/data/nePrikazujBiznisu/6");
        $response->assertRedirectToRoute("prijavaForma", [ "redirect" => "data/nePrikazujBiznisu/6" ]);

        $response = $this->post("/zahtevZaposlenje");
        $response->assertRedirectToRoute("prijavaForma", [ "redirect" => "zahtevZaposlenje" ]);

        $response = $this->post("/izmeniPodatkeB");
        $response->assertRedirectToRoute("prijavaForma", [ "redirect" => "izmeniPodatkeB" ]);

        $response = $this->get("/data/kalendarBiznis");
        $response->assertRedirectToRoute("prijavaForma", [ "redirect" => "data/kalendarBiznis" ]);

        $response = $this->post("/data/dodajNeradnoVreme");
        $response->assertRedirectToRoute("prijavaForma", [ "redirect" => "data/dodajNeradnoVreme" ]);

        $response = $this->get("/data/obrisiNeradnoVreme");
        $response->assertRedirectToRoute("prijavaForma", [ "redirect" => "data/obrisiNeradnoVreme" ]);

        $response = $this->get("/data/otkaziTermin");
        $response->assertRedirectToRoute("prijavaForma", [ "redirect" => "data/otkaziTermin" ]);

        $response = $this->get("data/uslugeZaposlenog/1");
        $response->assertRedirectToRoute("prijavaForma", [ "redirect" => "data/uslugeZaposlenog/1" ]);

        $response = $this->get("data/izmeniUslugu/1");
        $response->assertRedirectToRoute("prijavaForma", [ "redirect" => "data/izmeniUslugu/1" ]);

        $response = $this->get("data/izbrisiUslugu/1");
        $response->assertRedirectToRoute("prijavaForma", [ "redirect" => "data/izbrisiUslugu/1" ]);

        $response = $this->get("data/dodajUslugu/1");
        $response->assertRedirectToRoute("prijavaForma", [ "redirect" => "data/dodajUslugu/1" ]);

        // OSTALI TIPOVI KORISNIKA

        $admin = KorisnikModel::where("email", "admin@gmail.com")->first();
        $musterija = KorisnikModel::where("email", "musterija@gmail.com")->first();
        $zaposleni = KorisnikModel::where("email", "zaposleni@gmail.com")->first();

        $kao = [ $admin, $musterija, $zaposleni ];
        $guards = [ "admin", "musterija", "musterija" ];

        foreach (array_keys($kao) as $i) {
            $this->session([ "authGuard" => $guards[$i] ]);

            $response = $this->actingAs($kao[$i], $guards[$i])->get("/mojiZaposleni");
            $response->assertRedirectToRoute("prijavaForma", [ "redirect" => "mojiZaposleni" ]);

            $response = $this->actingAs($kao[$i], $guards[$i])->get("/zavrseniTermini");
            $response->assertRedirectToRoute("prijavaForma", [ "redirect" => "zavrseniTermini" ]);

            $response = $this->actingAs($kao[$i], $guards[$i])->get("/data/zavrseniTerminiBiznis");
            $response->assertRedirectToRoute("prijavaForma", [ "redirect" => "data/zavrseniTerminiBiznis" ]);

            $response = $this->actingAs($kao[$i], $guards[$i])->get("/data/biznisOceniTermin/6");
            $response->assertRedirectToRoute("prijavaForma", [ "redirect" => "data/biznisOceniTermin/6" ]);

            $response = $this->actingAs($kao[$i], $guards[$i])->get("/data/nePrikazujBiznisu/6");
            $response->assertRedirectToRoute("prijavaForma", [ "redirect" => "data/nePrikazujBiznisu/6" ]);

            $response = $this->actingAs($kao[$i], $guards[$i])->post("/zahtevZaposlenje");
            $response->assertRedirectToRoute("prijavaForma", [ "redirect" => "zahtevZaposlenje" ]);

            $response = $this->actingAs($kao[$i], $guards[$i])->post("/izmeniPodatkeB");
            $response->assertRedirectToRoute("prijavaForma", [ "redirect" => "izmeniPodatkeB" ]);

            $response = $this->actingAs($kao[$i], $guards[$i])->get("/data/kalendarBiznis");
            $response->assertRedirectToRoute("prijavaForma", [ "redirect" => "data/kalendarBiznis" ]);

            $response = $this->actingAs($kao[$i], $guards[$i])->post("/data/dodajNeradnoVreme");
            $response->assertRedirectToRoute("prijavaForma", [ "redirect" => "data/dodajNeradnoVreme" ]);

            $response = $this->actingAs($kao[$i], $guards[$i])->get("/data/obrisiNeradnoVreme");
            $response->assertRedirectToRoute("prijavaForma", [ "redirect" => "data/obrisiNeradnoVreme" ]);

            $response = $this->actingAs($kao[$i], $guards[$i])->get("/data/otkaziTermin");
            $response->assertRedirectToRoute("prijavaForma", [ "redirect" => "data/otkaziTermin" ]);

            $response = $this->actingAs($kao[$i], $guards[$i])->get("data/uslugeZaposlenog/1");
            $response->assertRedirectToRoute("prijavaForma", [ "redirect" => "data/uslugeZaposlenog/1" ]);

            $response = $this->actingAs($kao[$i], $guards[$i])->get("data/izmeniUslugu/1");
            $response->assertRedirectToRoute("prijavaForma", [ "redirect" => "data/izmeniUslugu/1" ]);

            $response = $this->actingAs($kao[$i], $guards[$i])->get("data/izbrisiUslugu/1");
            $response->assertRedirectToRoute("prijavaForma", [ "redirect" => "data/izbrisiUslugu/1" ]);

            $response = $this->actingAs($kao[$i], $guards[$i])->get("data/dodajUslugu/1");
            $response->assertRedirectToRoute("prijavaForma", [ "redirect" => "data/dodajUslugu/1" ]);
        }

        // BIZNISI

        $this->session([ "authGuard" => "biznis" ]);

        $biznis = KorisnikModel::where("email", "biznis@gmail.com")->first();

        $response = $this->actingAs($biznis, "biznis")->get("/mojiZaposleni");
        $response->assertStatus(200);

        $response = $this->actingAs($biznis, "biznis")->get("/zavrseniTermini");
        $response->assertStatus(200);

        $response = $this->actingAs($biznis, "biznis")->get("/data/zavrseniTerminiBiznis");
        $response->assertStatus(200);

        $response = $this->actingAs($biznis, "biznis")->get("/data/biznisOceniTermin/6");
        $response->assertNoContent();

        $response = $this->actingAs($biznis, "biznis")->get("/data/nePrikazujBiznisu/6");
        $response->assertNoContent();

        $this->actingAs($biznis, "biznis")->get("/mojiZaposleni");

        $response = $this->actingAs($biznis, "biznis")->post("/zahtevZaposlenje");
        $response->assertRedirectToRoute("mojiZaposleni");

        $this->actingAs($biznis, "biznis")->get("/profil");

        $response = $this->actingAs($biznis, "biznis")->post("/izmeniPodatkeB");
        $response->assertRedirectToRoute("profil");

        $response = $this->actingAs($biznis, "biznis")->get("/data/kalendarBiznis");
        $response->assertServerError();

        $response = $this->actingAs($biznis, "biznis")->post("/data/dodajNeradnoVreme");
        $response->assertStatus(400);

        $response = $this->actingAs($biznis, "biznis")->get("/data/obrisiNeradnoVreme");
        $response->assertNotFound();

        $response = $this->actingAs($biznis, "biznis")->get("/data/otkaziTermin");
        $response->assertNotFound();

        $response = $this->actingAs($biznis, "biznis")->get("data/uslugeZaposlenog/1");
        $response->assertStatus(200);

        $response = $this->actingAs($biznis, "biznis")->get("data/izmeniUslugu/1");
        $response->assertNoContent();

        $response = $this->actingAs($biznis, "biznis")->get("data/izbrisiUslugu/1");
        $response->assertNoContent();

        $response = $this->actingAs($biznis, "biznis")->get("data/dodajUslugu/1");
        $response->assertServerError();
    }

    // IZMENA PODATAKA

    /**
     * Testiranje renderovanja pravilnog view-a pri poseti stranice profila
     *
     * @return void
     */
    public function testIzmenaPodatakaView() {
        $this->seed(KorisniciSeeder::class);

        $biznis = KorisnikModel::where("email", "biznis@gmail.com")->first();

        $this->session([ "authGuard" => "biznis" ]);
        $response = $this->actingAs($biznis, "biznis")->get("/profil");

        $response->assertViewIs("biznis.profil");
    }

    /**
     * Testiranje validacije ulaza pri izmeni podataka biznisa
     *
     * @return void
     */
    public function testIzmenaPodatakaValidacija() {
        $this->seed(KorisniciSeeder::class);

        $biznis = KorisnikModel::where("email", "biznis@gmail.com")->first();

        $this->session([ "authGuard" => "biznis" ]);

        $response = $this->actingAs($biznis, "biznis")->post("/izmeniPodatkeB");

        $response->assertInvalid([ "zvanicnoIme", "email", "imeVlasnika", "prezimeVlasnika", "brojTelefona", "opis" ]);

        $response = $this->actingAs($biznis, "biznis")->post("/izmeniPodatkeB", [
            "zvanicnoIme" => "test1",
            "email" => "nevalidan mejl",
            "imeVlasnika" => "Ime.",
            "prezimeVlasnika" => "Prezime.",
            "PIB" => "0000",
            "brojTelefona" => "123456789",
            "opis" => "Opis"
        ]);

        $response->assertInvalid([ "email", "imeVlasnika", "prezimeVlasnika", "PIB" ]);

        $response = $this->actingAs($biznis, "biznis")->post("/izmeniPodatkeB", [
            "zvanicnoIme" => "Frizer Biznis",
            "email" => "biznis@gmail.com",
            "imeVlasnika" => "ime",
            "prezimeVlasnika" => "prezime",
            "PIB" => "000000000000",
            "brojTelefona" => "123456789",
            "opis" => "Opis"
        ]);

        $response->assertInvalid([ "PIB" ]);

        $response = $this->actingAs($biznis, "biznis")->post("/izmeniPodatkeB", [
            "zvanicnoIme" => "Frizer Biznis",
            "email" => "biznis@gmail.com",
            "imeVlasnika" => "Ime",
            "prezimeVlasnika" => "Prezime",
            "PIB" => "55555555",
            "brojTelefona" => "123456789",
            "opis" => "Opis",
            "novaLozinka" => "Test"
        ]);

        $response->assertInvalid([ "staraLozinka", "novaLozinka", "novaLozinkaP" ]);

        $response = $this->actingAs($biznis, "biznis")->post("/izmeniPodatkeB", [
            "zvanicnoIme" => "Frizer Biznis",
            "email" => "biznis@gmail.com",
            "imeVlasnika" => "Ime",
            "prezimeVlasnika" => "Prezime",
            "PIB" => "55555555",
            "brojTelefona" => "123456789",
            "opis" => "Opis",
            "staraLozinka" => "Testtesttesttesttest",
            "novaLozinka" => "Testtesttesttesttesttest",
            "novaLozinkaP" => "Testtesttesttesttesttest2"
        ]);

        $response->assertInvalid([ "novaLozinka", "novaLozinkaP" ]);

        $response = $this->actingAs($biznis, "biznis")->post("/izmeniPodatkeB", [
            "zvanicnoIme" => "Frizer Biznis",
            "email" => "biznis@gmail.com",
            "imeVlasnika" => "Ime",
            "prezimeVlasnika" => "Prezime",
            "PIB" => "55555555",
            "brojTelefona" => "123456789",
            "opis" => "Opis",
            "staraLozinka" => "Testtesttesttesttest",
            "novaLozinka" => "Lozinka1",
            "novaLozinkaP" => "Lozinka1."
        ]);

        $response->assertInvalid([ "novaLozinkaP" ]);

        $response = $this->actingAs($biznis, "biznis")->post("/izmeniPodatkeB", [
            "zvanicnoIme" => "Frizer Biznis",
            "email" => "biznis@gmail.com",
            "imeVlasnika" => "Ime",
            "prezimeVlasnika" => "Prezime",
            "PIB" => "55555555",
            "brojTelefona" => "123456789",
            "opis" => "Opis",
            "staraLozinka" => "Testtesttesttesttest",
            "novaLozinka" => "Lozinka1",
            "novaLozinkaP" => "Lozinka1"
        ]);

        $response->assertValid();
    }

    /**
     * Testiranje neuspešne izmene podataka: mejl već postoji
     *
     * @return void
     */
    public function testIzmenaPodatakaNeuspehDuplikat() {
        $this->seed(KorisniciSeeder::class);

        $biznis = KorisnikModel::where("email", "biznis@gmail.com")->first();

        $this->session([ "authGuard" => "biznis" ]);

        $this->actingAs($biznis, "biznis")->get("/profil");

        $response = $this->actingAs($biznis, "biznis")->post("/izmeniPodatkeB", [
            "zvanicnoIme" => "Frizer Biznis",
            "email" => "biznisneakt@gmail.com",
            "imeVlasnika" => "Ime",
            "prezimeVlasnika" => "Prezime",
            "PIB" => "55555555",
            "brojTelefona" => "123456789",
            "opis" => "Opis",
            "staraLozinka" => "Testtesttesttesttest",
            "novaLozinka" => "Lozinka1",
            "novaLozinkaP" => "Lozinka1"
        ]);

        $response->assertRedirectToRoute("profil");
        $response->assertSessionHas("customErr", [ "email" => "Već postoji nalog sa ovim email-om." ]);
    }

    /**
     * Testiranje neuspešne izmene podataka: stara lozinka netačna
     *
     * @return void
     */
    public function testIzmenaPodatakaNeuspehNetacnaLozinka() {
        $this->seed(KorisniciSeeder::class);

        $biznis = KorisnikModel::where("email", "biznis@gmail.com")->first();

        $this->session([ "authGuard" => "biznis" ]);

        $this->actingAs($biznis, "biznis")->get("/profil");

        $response = $this->actingAs($biznis, "biznis")->post("/izmeniPodatkeB", [
            "zvanicnoIme" => "Frizer Biznis",
            "email" => "biznis@gmail.com",
            "imeVlasnika" => "Ime",
            "prezimeVlasnika" => "Prezime",
            "PIB" => "55555555",
            "brojTelefona" => "123456789",
            "opis" => "Opis",
            "staraLozinka" => "netacna",
            "novaLozinka" => "Lozinka1",
            "novaLozinkaP" => "Lozinka1"
        ]);

        $response->assertRedirectToRoute("profil");
        $response->assertSessionHas("customErr", [ "staraLozinka" => "Unesena lozinka nije tačna." ]);
    }

    /**
     * Testiranje neuspešne izmene podataka: nova lozinka nema veliko slovo
     *
     * @return void
     */
    public function testIzmenaPodatakaNeuspehNemaVelikoSlovo() {
        $this->seed(KorisniciSeeder::class);

        $biznis = KorisnikModel::where("email", "biznis@gmail.com")->first();

        $this->session([ "authGuard" => "biznis" ]);

        $this->actingAs($biznis, "biznis")->get("/profil");

        $response = $this->actingAs($biznis, "biznis")->post("/izmeniPodatkeB", [
            "zvanicnoIme" => "Frizer Biznis",
            "email" => "biznis@gmail.com",
            "imeVlasnika" => "Ime",
            "prezimeVlasnika" => "Prezime",
            "PIB" => "55555555",
            "brojTelefona" => "123456789",
            "opis" => "Opis",
            "staraLozinka" => "biznis",
            "novaLozinka" => "lozinka1",
            "novaLozinkaP" => "lozinka1"
        ]);

        $response->assertRedirectToRoute("profil");
        $response->assertSessionHas("customErr", [ "novaLozinka" => "Lozinka mora sadržati bar jedno veliko slovo." ]);
    }

    /**
     * Testiranje neuspešne izmene podataka: ime vlasnika ne počinje velikim slovom
     *
     * @return void
     */
    public function testIzmenaPodatakaNeuspehImeVelikoSlovo() {
        $this->seed(KorisniciSeeder::class);

        $biznis = KorisnikModel::where("email", "biznis@gmail.com")->first();

        $this->session([ "authGuard" => "biznis" ]);

        $this->actingAs($biznis, "biznis")->get("/profil");

        $response = $this->actingAs($biznis, "biznis")->post("/izmeniPodatkeB", [
            "zvanicnoIme" => "Frizer Biznis",
            "email" => "biznis@gmail.com",
            "imeVlasnika" => "ime",
            "prezimeVlasnika" => "Prezime",
            "PIB" => "55555555",
            "brojTelefona" => "123456789",
            "opis" => "Opis",
            "staraLozinka" => "biznis",
            "novaLozinka" => "Lozinka1",
            "novaLozinkaP" => "Lozinka1"
        ]);

        $response->assertRedirectToRoute("profil");
        $response->assertSessionHas("customErr", [ "imeVlasnika" => "Ime mora da počinje velikim slovom." ]);
    }

    /**
     * Testiranje neuspešne izmene podataka: prezime vlasnika ne počinje velikim slovom
     *
     * @return void
     */
    public function testIzmenaPodatakaNeuspehPrezimeVelikoSlovo() {
        $this->seed(KorisniciSeeder::class);

        $biznis = KorisnikModel::where("email", "biznis@gmail.com")->first();

        $this->session([ "authGuard" => "biznis" ]);

        $this->actingAs($biznis, "biznis")->get("/profil");

        $response = $this->actingAs($biznis, "biznis")->post("/izmeniPodatkeB", [
            "zvanicnoIme" => "Frizer Biznis",
            "email" => "biznis@gmail.com",
            "imeVlasnika" => "Ime",
            "prezimeVlasnika" => "prezime",
            "PIB" => "55555555",
            "brojTelefona" => "123456789",
            "opis" => "Opis",
            "staraLozinka" => "biznis",
            "novaLozinka" => "Lozinka1",
            "novaLozinkaP" => "Lozinka1"
        ]);

        $response->assertRedirectToRoute("profil");
        $response->assertSessionHas("customErr", [ "prezimeVlasnika" => "Prezime mora da počinje velikim slovom." ]);
    }

    /**
     * Testiranje uspešne izmene podataka
     *
     * @return void
     */
    public function testIzmenaPodatakaUspeh() {
        $this->seed(KorisniciSeeder::class);

        $biznis = KorisnikModel::where("email", "biznis@gmail.com")->first();

        $this->session([ "authGuard" => "biznis" ]);

        $this->actingAs($biznis, "biznis")->get("/profil");

        $response = $this->actingAs($biznis, "biznis")->post("/izmeniPodatkeB", [
            "zvanicnoIme" => "Novi Biznis",
            "email" => "novibiznis@gmail.com",
            "imeVlasnika" => "Jovan",
            "prezimeVlasnika" => "Jovanović",
            "PIB" => "55555555",
            "brojTelefona" => "987654321",
            "opis" => "Novi Opis",
            "staraLozinka" => "biznis",
            "novaLozinka" => "Lozinka1",
            "novaLozinkaP" => "Lozinka1"
        ]);

        $biznis = BiznisModel::find($biznis->idKor);

        $this->assertEquals("Frizer Biznis", $biznis->zvanicnoIme);
        $this->assertEquals("Novi Biznis", $biznis->novoZvanicnoIme);

        $this->assertEquals("biznis@gmail.com", $biznis->email);
        $this->assertEquals("novibiznis@gmail.com", $biznis->noviEmail);

        $this->assertEquals("Lozinka1", $biznis->lozinka);

        $this->assertEquals("Jovan", $biznis->imeVlasnika);
        $this->assertEquals("Jovanović", $biznis->prezimeVlasnika);

        $this->assertEquals("55555555", $biznis->PIB);
        $this->assertEquals("987654321", $biznis->brojTelefona);
        $this->assertEquals("Novi Opis", $biznis->opis);

        $response->assertRedirectToRoute("profil");
        $response->assertSessionHas("status", 3);
    }

    /**
     * Testiranje uspešne izmene usluge
     *
     * @return void
     */
    public function testIzmenaUslugeUspeh(){
        $this->seed([KorisniciSeeder::class,
            UslugeSeeder::class
        ]);

        $biznis = KorisnikModel::where("email", "biznis@gmail.com")->first();
        $zaposleni = KorisnikModel::where("email", "zaposleni@gmail.com")->first();
        $usluga = UslugaModel::where("idCenovnik", 1)->first();

        $this->session([ "authGuard" => "biznis" ]);
        $this->actingAs($biznis, "biznis")->get("/mojiZaposleni?zap=" . $zaposleni->idKor);

        $response = $this->actingAs($biznis, "biznis")->get("/data/izmeniUslugu/" . $zaposleni->idKor . "?id=1&naziv=brijanje&cena=300&trajanje=10"
        );

        $usluga = UslugaModel::find($usluga->idCenovnik);

        $this->assertEquals("brijanje", $usluga->nazivUsluge);
        $this->assertEquals(300, $usluga->cena);
        $this->assertEquals(10, $usluga->trajanje);

    }

    public function testDodavanjeUsluge(){
        $this->seed([KorisniciSeeder::class,
            UslugeSeeder::class
        ]);

        $biznis = KorisnikModel::where("email", "biznis@gmail.com")->first();
        $zaposleni = KorisnikModel::where("email", "zaposleni@gmail.com")->first();

        $this->session([ "authGuard" => "biznis" ]);
        $this->actingAs($biznis, "biznis")->get("/mojiZaposleni?zap=" . $zaposleni->idKor);

        $response = $this->actingAs($biznis, "biznis")->get("/data/dodajUslugu/" . $zaposleni->idKor . "?naziv=farbanje&cena=600&trajanje=30"
        );

        $usluga = UslugaModel::latest("idCenovnik")->first();

        $this->assertEquals("farbanje", $usluga->nazivUsluge);
        $this->assertEquals(600, $usluga->cena);
        $this->assertEquals(30, $usluga->trajanje);
    }

    public function testBrisanjeUsluge(){
        $this->seed([KorisniciSeeder::class,
            UslugeSeeder::class
        ]);

        $biznis = KorisnikModel::where("email", "biznis@gmail.com")->first();
        $zaposleni = KorisnikModel::where("email", "zaposleni@gmail.com")->first();
        $brojUslugaPre = UslugaModel::all()->count();

        $this->session([ "authGuard" => "biznis" ]);
        $this->actingAs($biznis, "biznis")->get("/mojiZaposleni?zap=" . $zaposleni->idKor);

        $response = $this->actingAs($biznis, "biznis")->get("/data/izbrisiUslugu/" . $zaposleni->idKor . "?id=1"
        );

        $brojUslugaPosle = UslugaModel::all()->count();

        $this->assertNotEquals($brojUslugaPre, $brojUslugaPosle);
    }

    public function testOtkazivanjeTermina(){
        $this->seed([KorisniciSeeder::class,
            TerminiSeeder::class
        ]);

        $termin = TerminModel::all()[0];

        $biznis = KorisnikModel::find($termin->biznis->idKor);
        $zaposleni = $termin->zaposleni;

        $brojTerminaPre = TerminModel::all()->count();

        $this->session([ "authGuard" => "biznis" ]);

        $this->actingAs($biznis, "biznis")->get("/mojiZaposleni?zap=" . $zaposleni->idKor);

        $this->actingAs($biznis, "biznis")->get("/data/otkaziTermin?idTermin=" . $termin->idTermina);

        $brojTerminaPosle = TerminModel::all()->count();

        $this->assertLessThan($brojTerminaPre, $brojTerminaPosle);
    }

    public function testOceniTermin(){
        $this->seed([KorisniciSeeder::class,
            TerminiSeeder::class
        ]);

        $biznis = KorisnikModel::where("email", "biznis@gmail.com")->first();

        $this->session([ "authGuard" => "biznis" ]);
        $this->actingAs($biznis, "biznis")->get("/zavrseniTerminiBiznis");

        $response = $this->actingAs($biznis, "biznis")->get("/data/biznisOceniTermin/1" . "?ocena=5&komentar=Odlicna usluga, sve pohvale!");

        $termin = TerminModel::where("idTermina",1)->first();

        $this->assertEquals(5, $termin->ocenaBiznisa);
        $this->assertEquals("Odlicna usluga, sve pohvale!", $termin->komentarBiznisa);
        $this->assertEquals(0, $termin->prikaziBiznisu);
    }

    public function testOdbijOcenjivanjeKorisnika(){
        $this->seed([KorisniciSeeder::class,
            TerminiSeeder::class
        ]);

        $biznis = KorisnikModel::where("email", "biznis@gmail.com")->first();

        $this->session([ "authGuard" => "biznis" ]);
        $this->actingAs($biznis, "biznis")->get("/zavrseniTerminiBiznis");

        $response = $this->actingAs($biznis, "biznis")->get("/data/nePrikazujBiznisu/1");

        $termin = TerminModel::where("idTermina",1)->first();

        $this->assertEquals(0, $termin->prikaziBiznisu);
    }



    /**
     * Testiranje renderovanja pravilnog view-a pri poseti stranice zaposlenog
     *
     * @return void
     */
    public function testPregledKalendara()
    {
        $this->seed([
            KorisniciSeeder::class,
            ZaposlenjeSeeder::class
        ]);
        $biznis = KorisnikModel::where("email", "biznis@gmail.com")->first();
        $this->actingAs($biznis,"biznis");
        $this->session([ "authGuard" => "biznis" ]);
        
        $idZ=KorisnikModel::where("email", "zaposleni@gmail.com")->first()->idKor;
        $response = $this->get('/mojiZaposleni?zap='.$idZ);

        $response->assertViewIs('biznis.mojiZaposleni');
    }

    /**
     * Test pregleda kalendara zaposlenog
     *
     * @return void
     */
    public function testPregledKalendaraIUsluga()
    {
        $this->seed([
            KorisniciSeeder::class,
            ZaposlenjeSeeder::class
        ]);
        $biznis = KorisnikModel::where("email", "biznis@gmail.com")->first();
        $this->actingAs($biznis,"biznis");
        $this->session([ "authGuard" => "biznis" ]);
        
        $idZ=KorisnikModel::where("email", "zaposleni@gmail.com")->first()->idKor;
        $response = $this->get('/mojiZaposleni?zap='.$idZ);
        
        $response->assertSee("Nikola Nikolić");
        $response->assertSee("Cenovnik");
    }


    /**
     * Test uspešne izmene kalendara zaposlenog: uklanjanje termina
     *
     * @return void
     */
    public function testIzmenaKalendaraUspesnoUklanjanje()
    {
        $this->seed([
            KorisniciSeeder::class,
            ZaposlenjeSeeder::class
        ]);
        $biznis = KorisnikModel::where("email", "biznis@gmail.com")->first();
        $this->actingAs($biznis,"biznis");
        $this->session([ "authGuard" => "biznis" ]);
        
        $idZ=KorisnikModel::where("email", "zaposleni@gmail.com")->first()->idKor;
        $response = $this->get('/mojiZaposleni?zap='.$idZ);


        $response = $this->get('/data/obrisiNeradnoVreme?idNVreme='.
            NeradnoVremeModel::where("Biznis_Korisnik_idKor", $biznis->idKor)->first()->idNeradnoVreme
        );

        $response->assertStatus(204);
    }

    /**
     * Test uspešne izmene kalendara zaposlenog: dodavanje termina
     *
     * @return void
     */
    public function testIzmenaKalendaraUspesnoDodavanje()
    {
        $this->seed([
            KorisniciSeeder::class,
            ZaposlenjeSeeder::class
        ]);
        $biznis = KorisnikModel::where("email", "biznis@gmail.com")->first();
        $this->actingAs($biznis,"biznis");
        $this->session([ "authGuard" => "biznis" ]);
        
        $idZ=KorisnikModel::where("email", "zaposleni@gmail.com")->first()->idKor;
        $response = $this->get('/mojiZaposleni?zap='.$idZ);


        $response = $this->post('/data/dodajNeradnoVreme', [
            "zap" => $idZ,
            "pocetak" => intdiv((time()-5*24*60*60),600)*600,
            "kraj" => intdiv((time()-5*24*60*60),600)*600 + 1200
        ]);

        $response->assertStatus(204);
    }

    /**
     * Test neuspešne izmene kalendara zaposlenog: dodavanje termina koji traje prekratko
     *
     * @return void
     */
    public function testIzmenaKalendaraNeuspesnoPretkratko()
    {
        $this->seed([
            KorisniciSeeder::class,
            ZaposlenjeSeeder::class
        ]);
        $biznis = KorisnikModel::where("email", "biznis@gmail.com")->first();
        $this->actingAs($biznis,"biznis");
        $this->session([ "authGuard" => "biznis" ]);
        
        $idZ=KorisnikModel::where("email", "zaposleni@gmail.com")->first()->idKor;
        $response = $this->get('/mojiZaposleni?zap='.$idZ);


        $response = $this->post('/data/dodajNeradnoVreme', [
            "zap" => $idZ,
            "pocetak" => intdiv((time()-5*24*60*60),600)*600,
            "kraj" => intdiv((time()-5*24*60*60),600)*600 + 60
        ]);

        $response->assertStatus(400);
    }

    /**
     * Test neuspešne izmene kalendara zaposlenog: dodavanje termina koji ne počinje na XX:X0 (na inkrement od 10 minuta)
     *
     * @return void
     */
    public function testIzmenaKalendaraNeuspesnoNeporavnat()
    {
        $this->seed([
            KorisniciSeeder::class,
            ZaposlenjeSeeder::class
        ]);
        $biznis = KorisnikModel::where("email", "biznis@gmail.com")->first();
        $this->actingAs($biznis,"biznis");
        $this->session([ "authGuard" => "biznis" ]);
        
        $idZ=KorisnikModel::where("email", "zaposleni@gmail.com")->first()->idKor;
        $response = $this->get('/mojiZaposleni?zap='.$idZ);


        $response = $this->post('/data/dodajNeradnoVreme', [
            "zap" => $idZ,
            "pocetak" => intdiv((time()-5*24*60*60),600)*600 + 5,
            "kraj" => intdiv((time()-5*24*60*60),600)*600 + 1200
        ]);

        $response->assertStatus(400);
    }


    /**
     * Test neuspešne izmene kalendara zaposlenog: uklanjanje termina koji ne postoji
     *
     * @return void
     */
    public function testIzmenaKalendaraNeuspesnoUklanjanje()
    {
        $this->seed([
            KorisniciSeeder::class,
            ZaposlenjeSeeder::class
        ]);
        $biznis = KorisnikModel::where("email", "biznis@gmail.com")->first();
        $this->actingAs($biznis,"biznis");
        $this->session([ "authGuard" => "biznis" ]);
        
        $idZ=KorisnikModel::where("email", "zaposleni@gmail.com")->first()->idKor;
        $response = $this->get('/mojiZaposleni?zap='.$idZ);


        $response = $this->get('/data/obrisiNeradnoVreme?idNVreme=-1');

        $response->assertStatus(404);
    }

    /**
     * Test neuspešnog dodavanja zaposlenog: neispravan mejl
     *
     * @return void
     */
    public function testDodavanjeZaposlenogNeuspesno()
    {
        $this->seed(KorisniciSeeder::class);
        $biznis = KorisnikModel::where("email", "biznis@gmail.com")->first();
        $this->actingAs($biznis,"biznis");
        $this->session([ "authGuard" => "biznis" ]);

        $this->actingAs($biznis, "biznis")->get("/mojiZaposleni");

        $response = $this->actingAs($biznis, "biznis")->post("/zahtevZaposlenje", [
            "email" => "losmejl"
        ]);

        $response->assertRedirectToRoute("mojiZaposleni");
        $response->assertSessionHas("status", 1);
    }


    /**
     * Test uspešnog dodavanja zaposlenog
     *
     * @return void
     */
    public function testDodavanjeZaposlenogUspesno()
    {
        $this->seed(KorisniciSeeder::class);
        $biznis = KorisnikModel::where("email", "biznis@gmail.com")->first();
        $this->actingAs($biznis,"biznis");
        $this->session([ "authGuard" => "biznis" ]);

        $this->actingAs($biznis, "biznis")->get("/mojiZaposleni");


        $brojZaposlenjaPre = ZaposlenjeModel::all()->count();

        $response = $this->actingAs($biznis, "biznis")->post("/zahtevZaposlenje", [
            "email" => "mejl@novi.com"
        ]);

        $brojZaposlenjaPosle = ZaposlenjeModel::all()->count();

        $response->assertRedirectToRoute("mojiZaposleni");
        $response->assertSessionHas("status", 0);
        $this->assertNotEquals($brojZaposlenjaPre, $brojZaposlenjaPosle);
    }
}
