<?php

namespace Database\Seeders;

use App\Models\Korisnici\KorisnikModel;
use Carbon\Carbon;
use DateTime;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ZaposlenjeSeeder extends Seeder
{
    public function run(): void
    {
        DB::table("zaposlenje")->insert([
            "Biznis_Korisnik_idKor" => KorisnikModel::where("email", "biznis@gmail.com")->first()->idKor,
            "Zaposleni_Musterija_Korisnik_idKor" => KorisnikModel::where("email", "zaposleni@gmail.com")->first()->idKor,
            "zaposlen" => 1,
            "poslatZahtev" => 0
        ]);

        DB::table('usluga')->insert([
            "Biznis_Korisnik_idKor" => KorisnikModel::where("email", "biznis@gmail.com")->first()->idKor,
            "Zaposleni_Musterija_Korisnik_idKor" => KorisnikModel::where("email", "zaposleni@gmail.com")->first()->idKor,
            "nazivUsluge" => "sisanje",
            "cena" => 500,
            "trajanje" => 15
        ]);

        DB::table("neradnovreme")->insert([
            "Biznis_Korisnik_idKor" => KorisnikModel::where("email", "biznis@gmail.com")->first()->idKor,
            "Zaposleni_Musterija_Korisnik_idKor" => KorisnikModel::where("email", "zaposleni@gmail.com")->first()->idKor,
            "vremePocetka" => "2023-06-22 00:00:00",
            "vremeKraja" => "2023-06-22 07:00:00",
            "trajanje" => 420
        ]);

        DB::table("termin")->insert([
            "Biznis_Korisnik_idKor" => KorisnikModel::where("email", "biznis@gmail.com")->first()->idKor,
            "Zaposleni_Musterija_Korisnik_idKor" => KorisnikModel::where("email", "zaposleni@gmail.com")->first()->idKor,
            "Musterija_Korisnik_idKor" => KorisnikModel::where("email", "musterija@gmail.com")->first()->idKor,
            "vremePocetka" => DateTime::createFromFormat('Y-m-d H:i:s', "2023-06-22 12:00:00"),
            "vremeKraja" => DateTime::createFromFormat('Y-m-d H:i:s', "2023-06-22 12:20:00"),
            "trajanje" => 20,
            "prikaziKorisniku" => 1,
            "prikaziBiznisu" => 1
        ]);

        //PROSAO TERMIN

        DB::table("termin")->insert([
            "Biznis_Korisnik_idKor" => KorisnikModel::where("email", "biznis@gmail.com")->first()->idKor,
            "Zaposleni_Musterija_Korisnik_idKor" => KorisnikModel::where("email", "zaposleni@gmail.com")->first()->idKor,
            "Musterija_Korisnik_idKor" => KorisnikModel::where("email", "musterija@gmail.com")->first()->idKor,
            "vremePocetka" => DateTime::createFromFormat('Y-m-d H:i:s', "2023-06-12 12:00:00"),
            "vremeKraja" => DateTime::createFromFormat('Y-m-d H:i:s', "2023-06-12 12:20:00"),
            "trajanje" => 20,
            "prikaziKorisniku" => 1,
            "prikaziBiznisu" => 1
        ]);
    }
}
