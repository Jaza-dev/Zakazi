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
        Schema::create('biznis', function (Blueprint $table) {
            $table->integer('Korisnik_idKor')->primary();
            $table->string('zvanicnoIme', 45)->unique('zvanicnoIme_UNIQUE');
            $table->string('brojTelefona', 45);
            $table->string('imeVlasnika', 45);
            $table->string('prezimeVlasnika', 45);
            $table->integer('PIB')->nullable();
            $table->longText('opis')->nullable();
            $table->integer('TipBiznisa_idTipBiznisa')->index('fk_Biznis_TipBiznisa1_idx');
            $table->tinyInteger('verifikovan');
            $table->string('noviEmail', 45)->nullable();
            $table->string('novoZvanicnoIme', 45)->nullable();
            $table->tinyInteger('potvrdioIzmene')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('biznis');
    }
};
