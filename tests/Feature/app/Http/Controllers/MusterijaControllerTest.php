<?php

namespace Tests\Feature\app\Http\Controllers;

use App\Models\Korisnici\BiznisModel;
use App\Models\Korisnici\KorisnikModel;
use App\Models\Korisnici\MusterijaModel;
use App\Models\Korisnici\ZaposleniModel;
use App\Models\TerminModel;
use App\Models\UslugaModel;
use Carbon\Carbon;
use Database\Seeders\KorisniciSeeder;
use Database\Seeders\ZaposlenjeSeeder;
use DateTime;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Illuminate\Support\Facades\DB;

class MusterijaControllerTest extends TestCase
{
    use RefreshDatabase;
    public function testOdabirZaposlenog()
    {
        $this->seed([
            KorisniciSeeder::class,
            ZaposlenjeSeeder::class
        ]);

        $this->actingAs(KorisnikModel::where("email", "musterija@gmail.com")
            ->first(),"musterija");
        $this->session([ "authGuard" => "musterija" ]);
        
        $response = $this->get('/odaberiZaposlenog/'
            .KorisnikModel::where("email", "biznis@gmail.com")->first()->idKor.'/'
            .KorisnikModel::where("email", "zaposleni@gmail.com")->first()->idKor);

        $response->assertSee("Nikola Nikolić");
        $response->assertSee("Izaberite uslugu");
    }
    public function testOdabirUsluge()
    {
        $this->seed([
            KorisniciSeeder::class,
            ZaposlenjeSeeder::class
        ]);

        $this->actingAs(KorisnikModel::where("email", "musterija@gmail.com")
            ->first(),"musterija");
        $this->session([ "authGuard" => "musterija" ]);
        
        $idB=KorisnikModel::where("email", "biznis@gmail.com")->first()->idKor;
        $idZ=KorisnikModel::where("email", "zaposleni@gmail.com")->first()->idKor;
        $response = $this->get('/odaberiUslugu/'
            .$idB.'/'
            .$idZ.'/'
            .UslugaModel::where("Zaposleni_Musterija_Korisnik_idKor", $idZ)
            ->where("Biznis_Korisnik_idKor", $idB)->first()->idCenovnik);

        $response->assertSee("Nikola Nikolić");
        $response->assertSee("sisanje - Cena: 500 dinara - Trajanje: 15minuta");
    }
    public function testZakazivanjeTerminaUspeh()
    {
        $this->seed([
            KorisniciSeeder::class,
            ZaposlenjeSeeder::class
        ]);
        $m=KorisnikModel::where("email", "musterija@gmail.com")->first();
        $this->actingAs($m,"musterija");
        $this->session([ "authGuard" => "musterija" ]);
        
        $idB=KorisnikModel::where("email", "biznis@gmail.com")->first()->idKor;
        $idZ=KorisnikModel::where("email", "zaposleni@gmail.com")->first()->idKor;
        $response = $this->get('/zakaziTermin?idBiznisa='.$idB
                                .'&idZapsoleni='.$idZ
                                .'&idMusterija='.$m->idKor
                                .'&vremePocetka=2023-06-22 10:00:00'
                                .'&trajanje=15');
        //Carbon::createFromFormat('Y-m-d H:i:s', "2023-06-22 10:00:00")

        $response->assertStatus(204);
    }
    public function testZakazivanjeTerminaNeradnoVreme()
    {
        $this->seed([
            KorisniciSeeder::class,
            ZaposlenjeSeeder::class
        ]);
        $m=KorisnikModel::where("email", "musterija@gmail.com")->first();
        $this->actingAs($m,"musterija");
        $this->session([ "authGuard" => "musterija" ]);
        
        $idB=KorisnikModel::where("email", "biznis@gmail.com")->first()->idKor;
        $idZ=KorisnikModel::where("email", "zaposleni@gmail.com")->first()->idKor;
        $response = $this->get('/zakaziTermin?idBiznisa='.$idB
                                .'&idZapsoleni='.$idZ
                                .'&idMusterija='.$m->idKor
                                .'&vremePocetka=2023-06-22 00:00:00'
                                .'&trajanje=15');
        //Carbon::createFromFormat('Y-m-d H:i:s', "2023-06-22 10:00:00")

        $response->assertStatus(404);
    }
    public function testZakazivanjeTerminaZauzetTermin()
    {
        $this->seed([
            KorisniciSeeder::class,
            ZaposlenjeSeeder::class
        ]);
        $m=KorisnikModel::where("email", "musterija@gmail.com")->first();
        $this->actingAs($m,"musterija");
        $this->session([ "authGuard" => "musterija" ]);
        
        $idB=KorisnikModel::where("email", "biznis@gmail.com")->first()->idKor;
        $idZ=KorisnikModel::where("email", "zaposleni@gmail.com")->first()->idKor;
        $response = $this->get('/zakaziTermin?idBiznisa='.$idB
                                .'&idZapsoleni='.$idZ
                                .'&idMusterija='.$m->idKor
                                .'&vremePocetka=2023-06-22 12:00:00'
                                .'&trajanje=15');
        //Carbon::createFromFormat('Y-m-d H:i:s', "2023-06-22 10:00:00")

        $response->assertStatus(404);
    }
    public function testOcenjivanjeTermina()
    {
        $this->seed([
            KorisniciSeeder::class,
            ZaposlenjeSeeder::class
        ]);
        $m=KorisnikModel::where("email", "musterija@gmail.com")->first();
        $this->actingAs($m,"musterija");
        $this->session([ "authGuard" => "musterija" ]);

        $idB=KorisnikModel::where("email", "biznis@gmail.com")->first()->idKor;
        $idZ=KorisnikModel::where("email", "zaposleni@gmail.com")->first()->idKor;

        $response=$this->get('/odradjeniTermini');
        $response->assertSee("Odrađeni termini");
        $response->assertSee("2023-06-12 12:00:00");

        $response=$this->get('/musterijaOceniTermin/'.TerminModel::where("Biznis_Korisnik_idKor",$idB)
                            ->where("Zaposleni_Musterija_Korisnik_idKor",$idZ)
                            ->where("Musterija_Korisnik_idKor", $m->idKor)
                            ->where("vremePocetka","2023-06-12 12:00:00")
                            ->first()->idTermina
                        .'?ocena=3&komentar=komentar');
        $response->assertRedirectToRoute("odradjeniTermini");
        $response=$this->followRedirects($response);
        $response->assertSee("Odrađeni termini");
        $response->assertDontSee("2023-06-12 12:00:00");
    }
    public function testOdbacivanjeTermina()
    {
        $this->seed([
            KorisniciSeeder::class,
            ZaposlenjeSeeder::class
        ]);
        $m=KorisnikModel::where("email", "musterija@gmail.com")->first();
        $this->actingAs($m,"musterija");
        $this->session([ "authGuard" => "musterija" ]);

        $idB=KorisnikModel::where("email", "biznis@gmail.com")->first()->idKor;
        $idZ=KorisnikModel::where("email", "zaposleni@gmail.com")->first()->idKor;

        $response=$this->get('/odradjeniTermini');
        $response->assertSee("Odrađeni termini");
        $response->assertSee("2023-06-12 12:00:00");

        $response=$this->get('/nePrikazujKorisniku/'.TerminModel::where("Biznis_Korisnik_idKor",$idB)
                            ->where("Zaposleni_Musterija_Korisnik_idKor",$idZ)
                            ->where("Musterija_Korisnik_idKor", $m->idKor)
                            ->where("vremePocetka","2023-06-12 12:00:00")
                            ->first()->idTermina);
        $response->assertRedirectToRoute("odradjeniTermini");
        $response=$this->followRedirects($response);
        $response->assertSee("Odrađeni termini");
        $response->assertDontSee("2023-06-12 12:00:00");
    }

    public function testPretragaBiznisaPoImenuSortiranoPoOceni(){
        $this->seed(KorisniciSeeder::class);

        $m=KorisnikModel::where("email", "musterija@gmail.com")->first();
        $this->actingAs($m,"musterija");
        $this->session([ "authGuard" => "musterija" ]);

        $this->actingAs($m, "musterija")->get("/zakazi");

        $response = $this->actingAs($m, "musterija")->get("/pretraga?imeBiznisa=Frizer&sortiraj=poOceni");

        $response->assertSee("Frizer");
    }

    public function testPretragaBiznisaPoImenuSortiranoPoImenu(){
        $this->seed(KorisniciSeeder::class);

        $m=KorisnikModel::where("email", "musterija@gmail.com")->first();
        $this->actingAs($m,"musterija");
        $this->session([ "authGuard" => "musterija" ]);

        $this->actingAs($m, "musterija")->get("/zakazi");

        $response = $this->actingAs($m, "musterija")->get("/pretraga?imeBiznisa=Frizer&sortiraj=poImenu");

        $response->assertSeeInOrder(["Aca", "Biznis"]);
    }

    public function testPretragaBiznisaPoTipuBiznisaSortiranoPoOceni(){
        $this->seed(KorisniciSeeder::class);

        $m=KorisnikModel::where("email", "musterija@gmail.com")->first();
        $this->actingAs($m,"musterija");
        $this->session([ "authGuard" => "musterija" ]);

        $this->actingAs($m, "musterija")->get("/zakazi");

        $response = $this->actingAs($m, "musterija")->get("/pretraga?imeBiznisa=frizer&sortiraj=poOceni");

        $response->assertSee("Frizer Biznis");
    }

    public function testPretragaBiznisaPoTipuBiznisaSortiranoPoImenu(){
        $this->seed(KorisniciSeeder::class);

        $m=KorisnikModel::where("email", "musterija@gmail.com")->first();
        $this->actingAs($m,"musterija");
        $this->session([ "authGuard" => "musterija" ]);

        $this->actingAs($m, "musterija")->get("/zakazi");

        $response = $this->actingAs($m, "musterija")->get("/pretraga?imeBiznisa=frizer&sortiraj=poImenu");

        $response->assertSeeInOrder(["Aca", "Biznis"]);
    }



    /**
     * Testiranje renderovanja pravilnog view-a pri poseti stranice profila
     *
     * @return void
     */
    public function testIzmenaPodatakaView() {
        $this->seed(KorisniciSeeder::class);

        $musterija = KorisnikModel::where("email", "musterija@gmail.com")->first();

        $this->session([ "authGuard" => "musterija" ]);
        $response = $this->actingAs($musterija, "musterija")->get("/profil");

        $response->assertViewIs("musterija.profil");
    }

    /**
     * Testiranje validacije ulaza pri izmeni podataka biznisa
     *
     * @return void
     */
    public function testIzmenaPodatakaValidacija() {
        $this->seed(KorisniciSeeder::class);

        $musterija = KorisnikModel::where("email", "musterija@gmail.com")->first();

        $this->session([ "authGuard" => "musterija" ]);
        $response = $this->actingAs($musterija, "musterija")->post("/izmeniPodatkeM");

        $response->assertInvalid(["ime", "prezime", "korisnickoIme", "email"]);

        
        $response = $this->actingAs($musterija, "musterija")->post("/izmeniPodatkeM", [
            "ime" => "Nevalidno Ime123",
            "prezime" => "Prezime",
            "korisnickoIme" => "korisnickoime",
            "email" => "musterija@gmail.com"
        ]);

        $response->assertInvalid(["ime"]);

        $response = $this->actingAs($musterija, "musterija")->post("/izmeniPodatkeM", [
            "ime" => "Ime",
            "prezime" => "Prezime",
            "korisnickoIme" => "korisnickoime",
            "email" => "musterija@gmail.com",
            "staraLozinka" => "musterija",
            "novaLozinka" => "TestTestTestTestTest123",
            "novaLozinkaP" => "TestTestTestTestTest123"
        ]);

        $response->assertInvalid(["novaLozinka", "novaLozinkaP"]);

        $response = $this->actingAs($musterija, "musterija")->post("/izmeniPodatkeM", [
            "ime" => "Ime",
            "prezime" => "Prezime",
            "korisnickoIme" => "korisnickoime",
            "email" => "musterija@gmail.com",
            "staraLozinka" => "musterija",
            "novaLozinka" => "Lozinka123",
            "novaLozinkaP" => "Lozinka123."
        ]);

        $response->assertInvalid(["novaLozinkaP"]);

        $response = $this->actingAs($musterija, "musterija")->post("/izmeniPodatkeM", [
            "ime" => "Ime",
            "prezime" => "Prezime",
            "korisnickoIme" => "korisnickoime",
            "email" => "musterija@gmail.com",
            "staraLozinka" => "musterija",
            "novaLozinka" => "Lozinka123",
            "novaLozinkaP" => "Lozinka123"
        ]);

        $response->assertValid();
    }


    /**
     * Testiranje neuspešne izmene podataka: stara lozinka netačna
     *
     * @return void
     */
    public function testIzmenaPodatakaNeuspehNetacnaLozinka() {
        $this->seed(KorisniciSeeder::class);
        $musterija = KorisnikModel::where("email", "musterija@gmail.com")->first();
        $this->session([ "authGuard" => "musterija" ]);
        $this->actingAs($musterija, "musterija")->get("/profil");

        $response = $this->actingAs($musterija, "musterija")->post("/izmeniPodatkeM", [
            "ime" => "Ime",
            "prezime" => "Prezime",
            "korisnickoIme" => "korisnickoime",
            "email" => "musterija@gmail.com",
            "staraLozinka" => "musterijaNetacna",
            "novaLozinka" => "Lozinka123",
            "novaLozinkaP" => "Lozinka123"
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
        $musterija = KorisnikModel::where("email", "musterija@gmail.com")->first();
        $this->session([ "authGuard" => "musterija" ]);
        $this->actingAs($musterija, "musterija")->get("/profil");

        $response = $this->actingAs($musterija, "musterija")->post("/izmeniPodatkeM", [
            "ime" => "Ime",
            "prezime" => "Prezime",
            "korisnickoIme" => "korisnickoime",
            "email" => "musterija@gmail.com",
            "staraLozinka" => "musterija",
            "novaLozinka" => "nemavelikoslovo123",
            "novaLozinkaP" => "nemavelikoslovo123"
        ]);

        $response->assertRedirectToRoute("profil");
        $response->assertSessionHas("customErr", [ "novaLozinka" => "Lozinka mora sadržati bar jedno veliko slovo." ]);
    }

    /**
     * Testiranje neuspešne izmene podataka: ime ne počinje velikim slovom
     *
     * @return void
     */
    public function testIzmenaPodatakaNeuspehImeVelikoSlovo() {
        $this->seed(KorisniciSeeder::class);
        $musterija = KorisnikModel::where("email", "musterija@gmail.com")->first();
        $this->session([ "authGuard" => "musterija" ]);
        $this->actingAs($musterija, "musterija")->get("/profil");

        $response = $this->actingAs($musterija, "musterija")->post("/izmeniPodatkeM", [
            "ime" => "imebezvelikogslova",
            "prezime" => "Prezime",
            "korisnickoIme" => "korisnickoime",
            "email" => "musterija@gmail.com",
            "staraLozinka" => "musterija",
            "novaLozinka" => "Lozinka123",
            "novaLozinkaP" => "Lozinka123"
        ]);

        $response->assertRedirectToRoute("profil");
        $response->assertSessionHas("customErr", [ "ime" => "Ime mora da počinje velikim slovom." ]);
    }

    /**
     * Testiranje neuspešne izmene podataka: prezime ne počinje velikim slovom
     *
     * @return void
     */
    public function testIzmenaPodatakaNeuspehPrezimeVelikoSlovo() {
        $this->seed(KorisniciSeeder::class);
        $musterija = KorisnikModel::where("email", "musterija@gmail.com")->first();
        $this->session([ "authGuard" => "musterija" ]);
        $this->actingAs($musterija, "musterija")->get("/profil");

        $response = $this->actingAs($musterija, "musterija")->post("/izmeniPodatkeM", [
            "ime" => "Ime",
            "prezime" => "prezime",
            "korisnickoIme" => "korisnickoime",
            "email" => "musterija@gmail.com",
            "staraLozinka" => "musterija",
            "novaLozinka" => "Lozinka123",
            "novaLozinkaP" => "Lozinka123"
        ]);

        $response->assertRedirectToRoute("profil");
        $response->assertSessionHas("customErr", [ "prezime" => "Prezime mora da počinje velikim slovom." ]);
    }

    
    /**
     * Testiranje uspešne izmene podataka
     *
     * @return void
     */
    public function testIzmenaPodatakaUspeh() {
        $this->seed(KorisniciSeeder::class);
        $musterija = KorisnikModel::where("email", "musterija@gmail.com")->first();
        $this->session([ "authGuard" => "musterija" ]);
        $this->actingAs($musterija, "musterija")->get("/profil");

        $response = $this->actingAs($musterija, "musterija")->post("/izmeniPodatkeM", [
            "ime" => "Ime",
            "prezime" => "Prezime",
            "korisnickoIme" => "korisnickoime",
            "email" => "musterijaNovi@gmail.com",
            "staraLozinka" => "musterija",
            "novaLozinka" => "Lozinka123",
            "novaLozinkaP" => "Lozinka123"
        ]);

        $musterija = MusterijaModel::find($musterija->idKor);

        $this->assertEquals("Ime", $musterija->ime);
        $this->assertEquals("Prezime", $musterija->prezime);

        $this->assertEquals("korisnickoime", $musterija->korisnickoIme);

        $this->assertEquals("musterija@gmail.com", $musterija->email);
        $this->assertEquals("musterijaNovi@gmail.com", $musterija->noviEmail);

        $this->assertEquals("Lozinka123", $musterija->lozinka);

        $response->assertRedirectToRoute("profil");
        $response->assertSessionHas("status", 1);
    }
}
