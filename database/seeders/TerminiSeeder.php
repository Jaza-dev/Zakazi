<?php

namespace Database\Seeders;

use App\Models\Korisnici\BiznisModel;
use App\Models\Korisnici\MusterijaModel;
use App\Models\Korisnici\ZaposleniModel;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * TerminiSeeder – seeder baze koji kreira termine (buduće i prošle)
 *
 * @version 1.0
 */
class TerminiSeeder extends Seeder
{
    public function run() {
        $biznis1 = BiznisModel::all()[0];
        $biznis2 = BiznisModel::all()[1];

        $zap1 = ZaposleniModel::all()[0];
        $zap2 = ZaposleniModel::all()[1];

        $m1 = MusterijaModel::all()[0];
        $m2 = MusterijaModel::all()[1];

        // PROŠLI

        for ($i = 0; $i < 5; $i++) {
            DB::table("termin")->insert([
                "Biznis_Korisnik_idKor" => $i % 2 == 0 ? $biznis2->idKor : $biznis1->idKor,
                "Zaposleni_Musterija_Korisnik_idKor" => $i % 2 == 0 ? $zap1->idKor : $zap2->idKor,
                "Musterija_Korisnik_idKor" => $i % 2 == 0 ? $m1->idKor : $m2->idKor,
                "vremePocetka" => date("Y-m-d H:i:s", time() - 86400 + $i * 1200),
                "vremeKraja" => date("Y-m-d H:i:s", time() - 86400 + ($i + 1) * 1200),
                "trajanje" => 20,
                "prikaziKorisniku" => 1,
                "prikaziBiznisu" => 1
            ]);
        }

        // BUDUĆI

        for ($i = 0; $i < 5; $i++) {
            DB::table("termin")->insert([
                "Biznis_Korisnik_idKor" => $i % 2 == 0 ? $biznis2->idKor : $biznis1->idKor,
                "Zaposleni_Musterija_Korisnik_idKor" => $i % 2 == 0 ? $zap1->idKor : $zap2->idKor,
                "Musterija_Korisnik_idKor" => $i % 2 == 0 ? $m1->idKor : $m2->idKor,
                "vremePocetka" => date("Y-m-d H:i:s", time() + 86400 + $i * 1200),
                "vremeKraja" => date("Y-m-d H:i:s", time() + 86400 + ($i + 1) * 1200),
                "trajanje" => 20,
                "prikaziKorisniku" => 1,
                "prikaziBiznisu" => 1
            ]);
        }
    }
}
