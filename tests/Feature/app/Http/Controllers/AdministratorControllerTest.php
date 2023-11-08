<?php

namespace Tests\Feature\app\Http\Controllers;

use App\Models\Korisnici\KorisnikModel;
use App\Models\TipBiznisaModel;
use Database\Seeders\KorisniciSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AdministratorControllerTest extends TestCase
{
    use RefreshDatabase;
    public function testVerifikacijaBiznisa()
    {
        $this->seed([
            KorisniciSeeder::class
        ]);
        $a=KorisnikModel::where("email", "admin@gmail.com")->first();
        $this->actingAs($a,"admin");
        $this->session([ "authGuard" => "admin" ]);

        $response=$this->get('/verifikacijaBiznisa');
        $response->assertSee("biznisnever@gmail.com");

        $response=$this->post('/verifikuj/'
                            .KorisnikModel::where("email","biznisnever@gmail.com")
                            ->first()->idKor, [
                                'tipBiznisa' => "Frizer"
                            ]);
        $response->assertRedirectToRoute("index");
        $response=$this->followRedirects($response);
        $response->assertSee("Email biznisa");
        $response->assertDontSee("biznisnever@gmail.com");
    }
    public function testOdbijanjeVerifikacije()
    {
        $this->seed([
            KorisniciSeeder::class
        ]);
        $a=KorisnikModel::where("email", "admin@gmail.com")->first();
        $this->actingAs($a,"admin");
        $this->session([ "authGuard" => "admin" ]);

        $response=$this->get('/verifikacijaBiznisa');
        $response->assertSee("biznisnever@gmail.com");
        
        $response=$this->get('/odbij/'
                            .KorisnikModel::where("email","biznisnever@gmail.com")
                            ->first()->idKor);
        $response->assertRedirectToRoute("index");
        $response=$this->followRedirects($response);
        $response->assertSee("Email biznisa");
        $response->assertDontSee("biznisnever@gmail.com");
    }
    public function testDodavanjeNovogTipaBiznisa(){
        $this->seed([
            KorisniciSeeder::class
        ]);
        $a=KorisnikModel::where("email", "admin@gmail.com")->first();
        $this->actingAs($a,"admin");
        $this->session([ "authGuard" => "admin" ]);

        $response=$this->get('/verifikacijaBiznisa');
        $response->assertSee("biznisnever@gmail.com");
        $response->assertDontSee("Automehaničar");
        
        $response=$this->post('/dodajNoviTipBiznisa',[
            'imeTipaBiznisa' => 'Automehaničar'
        ]);

        $response->assertRedirectToRoute("index");
        $response=$this->followRedirects($response);
        $response->assertSee("Automehaničar");
    }
}
