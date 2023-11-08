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
        Schema::create('zaposlenje', function (Blueprint $table) {
            $table->integer('idZaposlenja', true);
            $table->integer('Biznis_Korisnik_idKor')->index('fk_Zaposlenje_Biznis1');
            $table->integer('Zaposleni_Musterija_Korisnik_idKor')->nullable()->index('fk_Zaposlenje_Zaposleni1_idx');
            $table->dateTime('poslatZahtev');
            $table->dateTime('prihvacenZahtev')->nullable();
            $table->tinyInteger('zaposlen');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('zaposlenje');
    }
};
