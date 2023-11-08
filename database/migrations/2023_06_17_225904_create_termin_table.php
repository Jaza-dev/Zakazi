<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('termin', function (Blueprint $table) {
            $table->integer('idTermina', true);
            $table->integer('Biznis_Korisnik_idKor')->index('fk_Termin_Biznis1_idx');
            $table->integer('Zaposleni_Musterija_Korisnik_idKor')->index('fk_Termin_Zaposleni1_idx');
            $table->integer('Musterija_Korisnik_idKor')->index('fk_Termin_Musterija1_idx');
            $table->dateTime('vremePocetka');
            $table->dateTime('vremeKraja');
            $table->integer('trajanje');
            $table->integer('ocenaKorisnika')->nullable();
            $table->longText('komentarKorisnika')->nullable();
            $table->integer('ocenaBiznisa')->nullable();
            $table->longText('komentarBiznisa')->nullable();
            $table->tinyInteger('prikaziKorisniku');
            $table->tinyInteger('prikaziBiznisu');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('termin');
    }
};
