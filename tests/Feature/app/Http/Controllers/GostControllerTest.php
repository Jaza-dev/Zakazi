<?php

namespace Tests\Feature\app\Http\Controllers;

use App\Models\Korisnici\KorisnikModel;
use App\Models\TipBiznisaModel;
use Database\Seeders\KorisniciSeeder;
use Database\Seeders\MejlVerifikacijaSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * GostControllerTest – klasa koja sadrži testove GostController-a
 *
 * @version 1.0
 */
class GostControllerTest extends TestCase {
    use RefreshDatabase;

    // PRISTUP

    /**
     * Testiranje ograničavanja pristupa
     *
     * @return void
     */
    public function testPristup() {
        $this->seed(KorisniciSeeder::class);

        // GOSTI IMAJU PRISTUP

        $response = $this->get("/registracijaKorisnika");
        $response->assertStatus(200);

        $response = $this->post("/registracijaKorisnika");
        $response->assertRedirectToRoute("registracijaKorisnika");

        $response = $this->get("/registracijaBiznisa");
        $response->assertStatus(200);

        $response = $this->post("/registracijaBiznisa");
        $response->assertRedirectToRoute("registracijaBiznisa");

        $response = $this->get("/prijava");
        $response->assertStatus(200);

        $response = $this->post("/prijava");
        $response->assertRedirectToRoute("prijavaForma");

        $response = $this->get("/posaljiMejl/1");
        $response->assertStatus(400);

        $response = $this->get("/mejlVerifikacija/".md5(microtime()));
        $response->assertRedirectToRoute("prijavaForma");

        // PRIJAVLJENI KORISNICI NEMAJU

        $admin = KorisnikModel::where("email", "admin@gmail.com")->first();
        $musterija = KorisnikModel::where("email", "musterija@gmail.com")->first();
        $biznis = KorisnikModel::where("email", "biznis@gmail.com")->first();
        $zaposleni = KorisnikModel::where("email", "zaposleni@gmail.com")->first();

        $kao = [ $admin, $musterija, $biznis, $zaposleni ];
        $guards = [ "admin", "musterija", "biznis", "musterija" ];

        foreach (array_keys($kao) as $i) {
            $response = $this->actingAs($kao[$i], $guards[$i])->get("/registracijaKorisnika");
            $response->assertRedirectToRoute("index");

            $response = $this->actingAs($kao[$i], $guards[$i])->post("/registracijaKorisnika");
            $response->assertRedirectToRoute("index");

            $response = $this->actingAs($kao[$i], $guards[$i])->get("/registracijaBiznisa");
            $response->assertRedirectToRoute("index");

            $response = $this->actingAs($kao[$i], $guards[$i])->post("/registracijaBiznisa");
            $response->assertRedirectToRoute("index");

            $response = $this->actingAs($kao[$i], $guards[$i])->get("/prijava");
            $response->assertRedirectToRoute("index");

            $response = $this->actingAs($kao[$i], $guards[$i])->post("/prijava");
            $response->assertRedirectToRoute("index");

            $response = $this->actingAs($kao[$i], $guards[$i])->get("/posaljiMejl/1");
            $response->assertRedirectToRoute("index");

            $response = $this->actingAs($kao[$i], $guards[$i])->get("/mejlVerifikacija/".md5(microtime()));
            $response->assertRedirectToRoute("index");
        }
    }

    // REGISTRACIJA: KORISNIK

    /**
     * Testiranje renderovanja pravilnog view-a pri poseti stranice za registraciju korisnika
     *
     * @return void
     */
    public function testRegistracijaKorisnikaView() {
        $response = $this->get("/registracijaKorisnika");

        $response->assertViewIs("musterija.registracija");
    }

    /**
     * Testiranje validacije ulaza pri registraciji korisnika
     *
     * @return void
     */
    public function testRegistracijaKorisnikaValidacija() {
        $response = $this->post("/registracijaKorisnika");

        $response->assertInvalid([ "korisnickoIme", "email", "lozinka", "ponovljenaLozinka", "ime", "prezime" ]);

        $response = $this->post("/registracijaKorisnika", [
            "korisnickoIme" => "test",
            "email" => "nevalidan mejl",
            "lozinka" => "Test",
            "ponovljenaLozinka" => "test2",
            "ime" => "Ime.",
            "prezime" => "Prezime."
        ]);

        $response->assertInvalid([ "korisnickoIme", "lozinka", "ponovljenaLozinka", "ime", "prezime" ]);

        $response = $this->post("/registracijaKorisnika", [
            "korisnickoIme" => "testtesttesttesttest",
            "email" => "test@gmail.com",
            "lozinka" => "testtestTesttesttesttest",
            "ponovljenaLozinka" => "testtestTesttesttesttest",
            "ime" => "ime",
            "prezime" => "prezime"
        ]);

        $response->assertInvalid([ "korisnickoIme", "lozinka", "ime", "prezime" ]);

        $response = $this->post("/registracijaKorisnika", [
            "korisnickoIme" => "korime.",
            "email" => "test@gmail.com",
            "lozinka" => "Lozinka1",
            "ponovljenaLozinka" => "Lozinka1",
            "ime" => "Ime",
            "prezime" => "Prezime"
        ]);

        $response->assertInvalid([ "korisnickoIme" ]);

        $response = $this->post("/registracijaKorisnika", [
            "korisnickoIme" => "korime",
            "email" => "test@gmail.com",
            "lozinka" => "Lozinka1",
            "ponovljenaLozinka" => "Lozinka1",
            "ime" => "Ime",
            "prezime" => "Prezime"
        ]);

        $response->assertValid();
    }

    /**
     * Testiranje neuspešne registracije korisnika: mejl već postoji
     *
     * @return void
     */
    public function testRegistracijaKorisnikaNeuspehDuplikat() {
        $this->seed(KorisniciSeeder::class);

        $this->get("/registracijaKorisnika");

        $response = $this->post("/registracijaKorisnika", [
            "korisnickoIme" => "musterija",
            "email" => "musterija@gmail.com",
            "lozinka" => "Lozinka1",
            "ponovljenaLozinka" => "Lozinka1",
            "ime" => "Petar",
            "prezime" => "Petrovic"
        ]);

        $response->assertRedirectToRoute("registracijaKorisnika");
        $response->assertSessionHas("status", "Već postoji nalog sa ovim email-om.");
    }

    /**
     * Testiranje uspešne registracije korisnika
     *
     * @return void
     */
    public function testRegistracijaKorisnikaUspeh() {
        $response = $this->post("/registracijaKorisnika", [
            "korisnickoIme" => "novamusterija",
            "email" => "novamusterija@gmail.com",
            "lozinka" => "Lozinka1",
            "ponovljenaLozinka" => "Lozinka1",
            "ime" => "Petar",
            "prezime" => "Petrovic"
        ]);

        $this->assertDatabaseHas("korisnik", [
            "email" => "novamusterija@gmail.com",
            "lozinka" => "Lozinka1",
            "tipKorisnika" => TIP_MUSTERIJA,
            "aktivan" => 0
        ]);

        $idKor = KorisnikModel::latest("idKor")->first()->idKor;

        $this->assertDatabaseHas("musterija", [
            "Korisnik_idKor" => $idKor,
            "korisnickoIme" => "novamusterija",
            "ime" => "Petar",
            "prezime" => "Petrovic"
        ]);

        $response->assertViewIs("verifikacijaObavestenje");

        $response->assertViewHas("email", "novamusterija@gmail.com");
        $response->assertViewHas("id", $idKor);
    }

    // REGISTRACIJA: BIZNIS

    /**
     * Testiranje renderovanja pravilnog view-a pri poseti stranice za registraciju biznisa
     *
     * @return void
     */
    public function testRegistracijaBiznisaView() {
        $response = $this->get("/registracijaBiznisa");

        $response->assertViewIs("biznis.registracija");
    }

    /**
     * Testiranje validacije ulaza pri registraciji biznisa
     *
     * @return void
     */
    public function testRegistracijaBiznisaValidacija() {
        $response = $this->post("/registracijaBiznisa");

        $response->assertInvalid([ "zvanicnoIme", "email", "lozinka", "ponovljenaLozinka", "imeVlasnika",
                                   "prezimeVlasnika", "tipBiznisa", "telefon", "opis" ]);

        $response = $this->post("/registracijaBiznisa", [
            "zvanicnoIme" => "test1",
            "email" => "nevalidan mejl",
            "lozinka" => "Test",
            "ponovljenaLozinka" => "test2",
            "imeVlasnika" => "Ime.",
            "prezimeVlasnika" => "Prezime.",
            "pib" => "0000",
            "tipBiznisa" => "test1",
            "telefon" => "123456789",
            "opis" => "Opis"
        ]);

        $response->assertInvalid([ "zvanicnoIme", "lozinka", "ponovljenaLozinka", "imeVlasnika",
                                   "prezimeVlasnika", "pib", "tipBiznisa" ]);

        $response = $this->post("/registracijaBiznisa", [
            "zvanicnoIme" => "Frizer Biznis",
            "email" => "biznis@gmail.com",
            "lozinka" => "testtestTesttesttesttest",
            "ponovljenaLozinka" => "testtestTesttesttesttest",
            "imeVlasnika" => "ime",
            "prezimeVlasnika" => "prezime",
            "pib" => "000000000",
            "tipBiznisa" => "Frizer",
            "telefon" => "123456789",
            "opis" => "Opis"
        ]);

        $response->assertInvalid([ "lozinka", "imeVlasnika", "prezimeVlasnika", "pib" ]);

        $response = $this->post("/registracijaBiznisa", [
            "zvanicnoIme" => "Frizer Biznis",
            "email" => "biznis@gmail.com",
            "lozinka" => "Lozinka1",
            "ponovljenaLozinka" => "Lozinka1",
            "imeVlasnika" => "Ime",
            "prezimeVlasnika" => "Prezime",
            "PIB" => "55555555",
            "tipBiznisa" => "Frizer",
            "telefon" => "123456789",
            "opis" => "Opis"
        ]);

        $response->assertValid();
    }

    /**
     * Testiranje neuspešne registracije biznisa: mejl već postoji
     *
     * @return void
     */
    public function testRegistracijaBiznisaNeuspehDupliMejl() {
        $this->seed(KorisniciSeeder::class);

        $this->get("/registracijaBiznisa");

        $response = $this->post("/registracijaBiznisa", [
            "zvanicnoIme" => "Frizer Biznis",
            "email" => "biznis@gmail.com",
            "lozinka" => "Lozinka1",
            "ponovljenaLozinka" => "Lozinka1",
            "imeVlasnika" => "Petar",
            "prezimeVlasnika" => "Petrovic",
            "tipBiznisa" => "Frizer",
            "telefon" => "123456789",
            "opis" => "Opis"
        ]);

        $response->assertRedirectToRoute("registracijaBiznisa");
        $response->assertSessionHas("status", "Već postoji nalog sa ovim email-om.");
    }

    /**
     * Testiranje neuspešne registracije biznisa: zvanično ime već postoji
     *
     * @return void
     */
    public function testRegistracijaBiznisaNeuspehDuploZvanicnoIme() {
        $this->seed(KorisniciSeeder::class);

        $this->get("/registracijaBiznisa");

        $response = $this->post("/registracijaBiznisa", [
            "zvanicnoIme" => "Frizer Biznis",
            "email" => "novibiznis@gmail.com",
            "lozinka" => "Lozinka1",
            "ponovljenaLozinka" => "Lozinka1",
            "imeVlasnika" => "Petar",
            "prezimeVlasnika" => "Petrovic",
            "tipBiznisa" => "Frizer",
            "telefon" => "123456789",
            "opis" => "Opis"
        ]);

        $response->assertRedirectToRoute("registracijaBiznisa");
        $response->assertSessionHas("status", "Već postoji biznis sa ovim imenom.");
    }

    /**
     * Testiranje uspešne registracije biznisa
     *
     * @return void
     */
    public function testRegistracijaBiznisaUspeh() {
        $this->get("/registracijaBiznisa");

        $response = $this->post("/registracijaBiznisa", [
            "zvanicnoIme" => "Novi Frizer Biznis",
            "email" => "novibiznis@gmail.com",
            "lozinka" => "Lozinka1",
            "ponovljenaLozinka" => "Lozinka1",
            "imeVlasnika" => "Petar",
            "prezimeVlasnika" => "Petrovic",
            "tipBiznisa" => "Frizer",
            "pib" => "12345678",
            "telefon" => "123456789",
            "opis" => "Opis"
        ]);

        $this->assertDatabaseHas("korisnik", [
            "email" => "novibiznis@gmail.com",
            "lozinka" => "Lozinka1",
            "tipKorisnika" => TIP_BIZNIS,
            "aktivan" => 0
        ]);

        $idKor = KorisnikModel::latest("idKor")->first()->idKor;

        $this->assertDatabaseHas("tipbiznisa", [
            "naziv" => "Frizer"
        ]);

        $this->assertDatabaseHas("biznis", [
            "Korisnik_idKor" => $idKor,
            "zvanicnoIme" => "Novi Frizer Biznis",
            "imeVlasnika" => "Petar",
            "prezimeVlasnika" => "Petrovic",
            "PIB" => "12345678",
            "brojTelefona" => "123456789",
            "opis" => "Opis",
            "verifikovan" => 0,
            "TipBiznisa_idTipBiznisa" => TipBiznisaModel::latest("idTipBiznisa")->first()->idTipBiznisa
        ]);

        $response->assertViewIs("verifikacijaObavestenje");

        $response->assertViewHas("email", "novibiznis@gmail.com");
        $response->assertViewHas("id", $idKor);
    }

    // PRIJAVA

    /**
     * Testiranje renderovanja pravilnog view-a pri poseti stranice za prijavu
     *
     * @return void
     */
    public function testPrijavaView() {
        $response = $this->get("/prijava");

        $response->assertViewIs("prijava");
    }

    /**
     * Testiranje ponašanja redirekcije na prijavu neautentifikovanih korisnika
     *
     * @return void
     */
    public function testRedirekcijaNaPrijavuNeautentifikovan() {
        $response = $this->get("/");

        $response->assertRedirectToRoute("prijavaForma");
    }

    /**
     * Testiranje ponašanja redirekcije na prijavu neautentifikovanih korisnika uz nameštanje linka za redirekciju
     *
     * @return void
     */
    public function testRedirekcijaNaPrijavuNeautentifikovanSaRedirect() {
        $response = $this->get("/profil");

        $response->assertRedirectToRoute("prijavaForma", [ "redirect" => "profil" ]);
    }

    /**
     * Testiranje neuspešne prijave: nepostojeći mejl
     *
     * @return void
     */
    public function testPrijavaNeuspehMejl() {
        $this->seed(KorisniciSeeder::class);

        $response = $this->post("/prijava", [
            "email" => "nepostojeci@gmail.com",
            "lozinka" => "lozinka"
        ]);

        $this->assertGuest();
        $response->assertSessionHas("status", "Niste dobro uneli email ili lozinku.");
    }

    /**
     * Testiranje neuspešne prijave: pogrešna lozinka
     *
     * @return void
     */
    public function testPrijavaNeuspehLozinka() {
        $this->seed(KorisniciSeeder::class);

        $response = $this->post("/prijava", [
            "email" => "admin@gmail.com",
            "lozinka" => "greska"
        ]);

        $this->assertGuest();
        $response->assertSessionHas("status", "Niste dobro uneli email ili lozinku.");
    }

    /**
     * Testiranje uspešne prijave kao administrator
     *
     * @return void
     */
    public function testPrijavaAdminUspeh() {
        $this->seed(KorisniciSeeder::class);

        $response = $this->post("/prijava", [
            "email" => "admin@gmail.com",
            "lozinka" => "admin"
        ]);

        $this->assertAuthenticated("admin");
        $response->assertRedirectToRoute("index");
    }

    /**
     * Testiranje neuspešne prijave kao neaktivan administrator
     *
     * @return void
     */
    public function testPrijavaAdminNeaktivan() {
        $this->seed(KorisniciSeeder::class);

        $response = $this->post("/prijava", [
            "email" => "adminneakt@gmail.com",
            "lozinka" => "admin"
        ]);

        $this->assertGuest();
        $response->assertSessionHas("status", "Niste verifikovali email.");
    }

    /**
     * Testiranje uspešne prijave kao mušterija
     *
     * @return void
     */
    public function testPrijavaMusterijaUspeh() {
        $this->seed(KorisniciSeeder::class);

        $response = $this->post("/prijava", [
            "email" => "musterija@gmail.com",
            "lozinka" => "musterija"
        ]);

        $this->assertAuthenticated("musterija");
        $response->assertRedirectToRoute("index");
    }

    /**
     * Testiranje neuspešne prijave kao neaktivna mušterija
     *
     * @return void
     */
    public function testPrijavaMusterijaNeaktivna() {
        $this->seed(KorisniciSeeder::class);

        $response = $this->post("/prijava", [
            "email" => "musterijaneakt@gmail.com",
            "lozinka" => "musterija"
        ]);

        $this->assertGuest();
        $response->assertSessionHas("status", "Niste verifikovali email.");
    }

    /**
     * Testiranje uspešne prijave kao biznis
     *
     * @return void
     */
    public function testPrijavaBiznisUspeh() {
        $this->seed(KorisniciSeeder::class);

        $response = $this->post("/prijava", [
            "email" => "biznis@gmail.com",
            "lozinka" => "biznis"
        ]);

        $this->assertAuthenticated("biznis");
        $response->assertRedirectToRoute("index");
    }

    /**
     * Testiranje neuspešne prijave kao neaktivan biznis
     *
     * @return void
     */
    public function testPrijavaBiznisNeaktivan() {
        $this->seed(KorisniciSeeder::class);

        $response = $this->post("/prijava", [
            "email" => "biznisneakt@gmail.com",
            "lozinka" => "biznis"
        ]);

        $this->assertGuest();
        $response->assertSessionHas("status", "Niste verifikovali email.");
    }

    /**
     * Testiranje neuspešne prijave kao neverifikovan biznis
     *
     * @return void
     */
    public function testPrijavaBiznisNeverifikovan() {
        $this->seed(KorisniciSeeder::class);

        $response = $this->post("/prijava", [
            "email" => "biznisnever@gmail.com",
            "lozinka" => "biznis"
        ]);

        $this->assertGuest();
        $response->assertSessionHas("status", "Vaš biznis nije verifikovan od strane administratora. Molimo pokušajte kasnije.");
    }

    /**
     * Testiranje neuspešne prijave kao biznis kome je verifikacija odbijena
     *
     * @return void
     */
    public function testPrijavaBiznisOdbijen() {
        $this->seed(KorisniciSeeder::class);

        $response = $this->post("/prijava", [
            "email" => "biznisodbijen@gmail.com",
            "lozinka" => "biznis"
        ]);

        $this->assertGuest();
        $response->assertSessionHas("status", "Vaša registracija biznisa je odbijena od strane administratora.");
    }

    /**
     * Testiranje uspešne prijave kao zaposleni
     *
     * @return void
     */
    public function testPrijavaZaposleniUspeh() {
        $this->seed(KorisniciSeeder::class);

        $response = $this->post("/prijava", [
            "email" => "zaposleni@gmail.com",
            "lozinka" => "zaposleni"
        ]);

        $this->assertAuthenticated("musterija");
        $response->assertRedirectToRoute("index");

        $response = $this->get("/mojKalendar");

        $response->assertStatus(200);
    }

    /**
     * Testiranje neuspešne prijave kao neaktivan zaposleni
     *
     * @return void
     */
    public function testPrijavaZaposleniNeaktivan() {
        $this->seed(KorisniciSeeder::class);

        $response = $this->post("/prijava", [
            "email" => "zaposlenineakt@gmail.com",
            "lozinka" => "zaposleni"
        ]);

        $this->assertGuest();
        $response->assertSessionHas("status", "Niste verifikovali email.");
    }

    // MEJL VERIFIKACIJA

    /**
     * Testiranje ponovnog slanja mejla
     *
     * @return void
     */
    public function testPonovnoSlanjeMejla() {
        $this->seed(KorisniciSeeder::class);

        $kor = KorisnikModel::where("aktivan", 0)->first();

        $response = $this->get("/posaljiMejl/".$kor->idKor);

        $response->assertViewIs("verifikacijaObavestenje");

        $response->assertViewHas("email", $kor->email);
        $response->assertViewHas("id", $kor->idKor);
    }

    /**
     * Testiranje verifikacije mejla pri registraciji: neuspeh
     *
     * @return void
     */
    public function testVerifikacijaMejlaRegistracijaNeuspehNepostojeciHash() {
        $this->seed([
            KorisniciSeeder::class,
            MejlVerifikacijaSeeder::class
        ]);

        $kor = KorisnikModel::where("email", "musterijaneakt@gmail.com")->first();

        $this->assertTrue($kor->aktivan == 0);

        $response = $this->get("/mejlVerifikacija/".md5("ne postoji"));

        $kor->refresh();

        $this->assertTrue($kor->aktivan == 0);

        $response->assertRedirectToRoute("prijavaForma");
    }

    /**
     * Testiranje verifikacije mejla pri registraciji: uspeh
     *
     * @return void
     */
    public function testVerifikacijaMejlaRegistracijaUspeh() {
        $this->seed([
            KorisniciSeeder::class,
            MejlVerifikacijaSeeder::class
        ]);

        $kor = KorisnikModel::where("email", "musterijaneakt@gmail.com")->first();

        $this->assertTrue($kor->aktivan == 0);

        $response = $this->get("/mejlVerifikacija/".md5("test registracija"));

        $kor->refresh();

        $this->assertTrue($kor->aktivan == 1);
        $this->assertDatabaseCount("mejlverifikacija", 0);

        $response->assertViewIs("porukaRedirect");
        $response->assertViewHas("poruka", "Uspešno potvrđena mejl adresa!");
    }
}
