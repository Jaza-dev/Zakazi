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
        Schema::table('zaposleni', function (Blueprint $table) {
            $table->foreign(['Musterija_Korisnik_idKor'], 'fk_Zaposleni_Musterija1')->references(['Korisnik_idKor'])->on('musterija')->onUpdate('NO ACTION')->onDelete('NO ACTION');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('zaposleni', function (Blueprint $table) {
            $table->dropForeign('fk_Zaposleni_Musterija1');
        });
    }
};
