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
        Schema::table('zaposlenje', function (Blueprint $table) {
            $table->foreign(['Biznis_Korisnik_idKor'], 'fk_Zaposlenje_Biznis1')->references(['Korisnik_idKor'])->on('biznis')->onUpdate('NO ACTION')->onDelete('NO ACTION');
            $table->foreign(['Zaposleni_Musterija_Korisnik_idKor'], 'fk_Zaposlenje_Zaposleni1')->references(['Musterija_Korisnik_idKor'])->on('zaposleni')->onUpdate('NO ACTION')->onDelete('NO ACTION');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('zaposlenje', function (Blueprint $table) {
            $table->dropForeign('fk_Zaposlenje_Biznis1');
            $table->dropForeign('fk_Zaposlenje_Zaposleni1');
        });
    }
};
