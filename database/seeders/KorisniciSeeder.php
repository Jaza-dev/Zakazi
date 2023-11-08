<?php

namespace Database\Seeders;

use App\Models\Korisnici\KorisnikModel;
use App\Models\TipBiznisaModel;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * KorisniciSeeder – seeder baze koji kreira različite korisnike
 *
 * @version 1.0
 */
class KorisniciSeeder extends Seeder
{
    /**
     * Kreiranje više različitih administratora (aktivan i neaktivan)
     *
     * @return void
     */
    private function kreirajAdministratore() {
        // AKTIVAN

        DB::table("korisnik")->insert([
            "email" => "admin@gmail.com",
            "lozinka" => "admin",
            "aktivan" => 1,
            "tipKorisnika" => 0 // TIP_ADMIN
        ]);

        DB::table("administrator")->insert([
            "Korisnik_idKor" => KorisnikModel::latest("idKor")->first()->idKor
        ]);

        // NEAKTIVAN

        DB::table("korisnik")->insert([
            "email" => "adminneakt@gmail.com",
            "lozinka" => "admin",
            "aktivan" => 0,
            "tipKorisnika" => 0 // TIP_ADMIN
        ]);

        DB::table("administrator")->insert([
            "Korisnik_idKor" => KorisnikModel::latest("idKor")->first()->idKor
        ]);
    }

    /**
     * Kreiranje više različitih mušterija (aktivna i neaktivna)
     *
     * @return void
     */
    private function kreirajMusterije() {
        // AKTIVNA

        DB::table("korisnik")->insert([
            "email" => "musterija@gmail.com",
            "lozinka" => "musterija",
            "aktivan" => 1,
            "tipKorisnika" => 1 // TIP_MUSTERIJA
        ]);

        DB::table("musterija")->insert([
            "Korisnik_idKor" => KorisnikModel::latest("idKor")->first()->idKor,
            "korisnickoIme" => "musterija",
            "ime" => "Petar",
            "prezime" => "Petrović"
        ]);

        // NEAKTIVNA

        DB::table("korisnik")->insert([
            "email" => "musterijaneakt@gmail.com",
            "lozinka" => "musterija",
            "aktivan" => 0,
            "tipKorisnika" => 1 // TIP_MUSTERIJA
        ]);

        DB::table("musterija")->insert([
            "Korisnik_idKor" => KorisnikModel::latest("idKor")->first()->idKor,
            "korisnickoIme" => "musterija",
            "ime" => "Petar",
            "prezime" => "Petrović"
        ]);
    }

    /**
     * Kreiranje više različitih biznisa (aktivan verifikovan, aktivan neverifikovan, verifikacija odbijena i neaktivan)
     *
     * @return void
     */
    private function kreirajBiznise() {
        // AKTIVAN, VERIFIKOVAN

        DB::table("korisnik")->insert([
            "email" => "biznis@gmail.com",
            "lozinka" => "biznis",
            "aktivan" => 1,
            "tipKorisnika" => 2 // TIP_BIZNIS
        ]);

        DB::table("tipbiznisa")->insert([
            "naziv" => "Frizer"
        ]);

        DB::table("biznis")->insert([
            "Korisnik_idKor" => KorisnikModel::latest("idKor")->first()->idKor,
            "zvanicnoIme" => "Frizer Biznis",
            "brojTelefona" => "123456789",
            "imeVlasnika" => "Jovan",
            "prezimeVlasnika" => "Jovanović",
            "PIB" => "12345678",
            "opis" => "Opis Frizer Biznisa",
            "TipBiznisa_idTipBiznisa" => TipBiznisaModel::latest("idTipBiznisa")->first()->idTipBiznisa,
            "verifikovan" => 1
        ]);
        //drugi
        DB::table("korisnik")->insert([
            "email" => "biznis2@gmail.com",
            "lozinka" => "biznis",
            "aktivan" => 1,
            "tipKorisnika" => 2 // TIP_BIZNIS
        ]);

        DB::table("tipbiznisa")->insert([
            "naziv" => "Frizer"
        ]);

        DB::table("biznis")->insert([
            "Korisnik_idKor" => KorisnikModel::latest("idKor")->first()->idKor,
            "zvanicnoIme" => "Frizer Aca",
            "brojTelefona" => "123456789",
            "imeVlasnika" => "Aca",
            "prezimeVlasnika" => "Acic",
            "PIB" => "12345678",
            "opis" => "Opis Frizer Biznisa",
            "TipBiznisa_idTipBiznisa" => TipBiznisaModel::latest("idTipBiznisa")->first()->idTipBiznisa,
            "verifikovan" => 1
        ]);

        // NEAKTIVAN

        DB::table("korisnik")->insert([
            "email" => "biznisneakt@gmail.com",
            "lozinka" => "biznis",
            "aktivan" => 0,
            "tipKorisnika" => 2 // TIP_BIZNIS
        ]);

        DB::table("tipbiznisa")->insert([
            "naziv" => "Majstor"
        ]);

        DB::table("biznis")->insert([
            "Korisnik_idKor" => KorisnikModel::latest("idKor")->first()->idKor,
            "zvanicnoIme" => "Majstor Biznis",
            "brojTelefona" => "123456789",
            "imeVlasnika" => "Jovan",
            "prezimeVlasnika" => "Jovanović",
            "PIB" => "12345678",
            "opis" => "Opis Majstor Biznisa",
            "TipBiznisa_idTipBiznisa" => TipBiznisaModel::latest("idTipBiznisa")->first()->idTipBiznisa,
            "verifikovan" => 0
        ]);

        // AKTIVAN, NEVERIFIKOVAN

        DB::table("korisnik")->insert([
            "email" => "biznisnever@gmail.com",
            "lozinka" => "biznis",
            "aktivan" => 1,
            "tipKorisnika" => 2 // TIP_BIZNIS
        ]);

        DB::table("tipbiznisa")->insert([
            "naziv" => "Maser"
        ]);

        DB::table("biznis")->insert([
            "Korisnik_idKor" => KorisnikModel::latest("idKor")->first()->idKor,
            "zvanicnoIme" => "Maser Biznis",
            "brojTelefona" => "123456789",
            "imeVlasnika" => "Jovan",
            "prezimeVlasnika" => "Jovanović",
            "PIB" => "12345678",
            "opis" => "Opis Maser Biznisa",
            "TipBiznisa_idTipBiznisa" => TipBiznisaModel::latest("idTipBiznisa")->first()->idTipBiznisa,
            "verifikovan" => 0
        ]);

        // AKTIVAN, ODBIJENA VERIFIKACIJA

        DB::table("korisnik")->insert([
            "email" => "biznisodbijen@gmail.com",
            "lozinka" => "biznis",
            "aktivan" => 1,
            "tipKorisnika" => 2 // TIP_BIZNIS
        ]);

        DB::table("tipbiznisa")->insert([
            "naziv" => "Zubar"
        ]);

        DB::table("biznis")->insert([
            "Korisnik_idKor" => KorisnikModel::latest("idKor")->first()->idKor,
            "zvanicnoIme" => "Zubar Biznis",
            "brojTelefona" => "123456789",
            "imeVlasnika" => "Jovan",
            "prezimeVlasnika" => "Jovanović",
            "PIB" => "12345678",
            "opis" => "Opis Zubar Biznisa",
            "TipBiznisa_idTipBiznisa" => TipBiznisaModel::latest("idTipBiznisa")->first()->idTipBiznisa,
            "verifikovan" => -1
        ]);
    }

    /**
     * Kreiranje više različitih zaposlenih (aktivan i neaktivan)
     *
     * @return void
     */
    private function kreirajZaposlene() {
        // AKTIVAN

        DB::table("korisnik")->insert([
            "email" => "zaposleni@gmail.com",
            "lozinka" => "zaposleni",
            "aktivan" => 1,
            "tipKorisnika" => 3 // TIP_ZAPOSLENI
        ]);

        DB::table("musterija")->insert([
            "Korisnik_idKor" => KorisnikModel::latest("idKor")->first()->idKor,
            "korisnickoIme" => "zaposleni",
            "ime" => "Nikola",
            "prezime" => "Nikolić"
        ]);

        DB::table("zaposleni")->insert([
            "Musterija_Korisnik_idKor" => KorisnikModel::latest("idKor")->first()->idKor
        ]);

        // NEAKTIVAN

        DB::table("korisnik")->insert([
            "email" => "zaposlenineakt@gmail.com",
            "lozinka" => "zaposleni",
            "aktivan" => 0,
            "tipKorisnika" => 3 // TIP_ZAPOSLENI
        ]);

        DB::table("musterija")->insert([
            "Korisnik_idKor" => KorisnikModel::latest("idKor")->first()->idKor,
            "korisnickoIme" => "zaposleni",
            "ime" => "Nikola",
            "prezime" => "Nikolić"
        ]);

        DB::table("zaposleni")->insert([
            "Musterija_Korisnik_idKor" => KorisnikModel::latest("idKor")->first()->idKor
        ]);
    }

    public function run() {
        $this->kreirajAdministratore();
        $this->kreirajMusterije();
        $this->kreirajBiznise();
        $this->kreirajZaposlene();
    }
}
