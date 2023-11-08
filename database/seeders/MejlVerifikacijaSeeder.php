<?php

namespace Database\Seeders;

use App\Models\Korisnici\KorisnikModel;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * MejlVerifikacijaSeeder – seeder baze koji kreira ulaze za mejl verifikaciju
 *
 * @version 1.0
 */
class MejlVerifikacijaSeeder extends Seeder
{
    public function run() {
        // REGISTRACIJA

        $kor = KorisnikModel::where("email", "musterijaneakt@gmail.com")->first();

        DB::table("mejlverifikacija")->insert([
            "hash" => md5("test registracija"),
            "Korisnik_idKor" => $kor->idKor
        ]);
    }
}
