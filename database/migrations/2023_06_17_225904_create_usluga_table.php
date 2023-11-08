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
        Schema::create('usluga', function (Blueprint $table) {
            $table->integer('idCenovnik', true);
            $table->integer('Zaposleni_Musterija_Korisnik_idKor')->index('fk_Cenovnik_Zaposleni1_idx');
            $table->integer('Biznis_Korisnik_idKor')->index('fk_Cenovnik_Biznis1_idx');
            $table->string('nazivUsluge', 45);
            $table->integer('cena');
            $table->integer('trajanje');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('usluga');
    }
};
