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
        Schema::create('neradnovreme', function (Blueprint $table) {
            $table->integer('idNeradnoVreme', true);
            $table->integer('Biznis_Korisnik_idKor')->index('fk_NeradnoVreme_Biznis1_idx');
            $table->integer('Zaposleni_Musterija_Korisnik_idKor')->index('fk_NeradnoVreme_Zaposleni1_idx');
            $table->dateTime('vremePocetka');
            $table->dateTime('vremeKraja');
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
        Schema::dropIfExists('neradnovreme');
    }
};
