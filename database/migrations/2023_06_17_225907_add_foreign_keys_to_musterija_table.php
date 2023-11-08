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
        Schema::table('musterija', function (Blueprint $table) {
            $table->foreign(['Korisnik_idKor'], 'fk_Musterija_Korisnik1')->references(['idKor'])->on('korisnik')->onUpdate('NO ACTION')->onDelete('NO ACTION');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('musterija', function (Blueprint $table) {
            $table->dropForeign('fk_Musterija_Korisnik1');
        });
    }
};
