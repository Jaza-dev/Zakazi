<?php

namespace Database\Seeders;

use App\Models\Korisnici\UslugeModel;
use App\Models\Korisnici\KorisnikModel;
use Carbon\Carbon;
use DateTime;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UslugeSeeder extends Seeder
{
    public function run(): void
    {
        DB::table("usluga")->insert([
            "nazivUsluge" => "sisanje",
            "cena" => 500,
            "trajanje" => 20,
            "Biznis_Korisnik_idKor" => KorisnikModel::where("email", "biznis@gmail.com")->first()->idKor,
            "Zaposleni_Musterija_Korisnik_idKor" => KorisnikModel::where("email", "zaposleni@gmail.com")->first()->idKor
        ]);
    }
}
