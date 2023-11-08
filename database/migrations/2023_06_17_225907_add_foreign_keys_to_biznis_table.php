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
        Schema::table('biznis', function (Blueprint $table) {
            $table->foreign(['Korisnik_idKor'], 'fk_Biznis_Korisnik')->references(['idKor'])->on('korisnik')->onUpdate('NO ACTION')->onDelete('NO ACTION');
            $table->foreign(['TipBiznisa_idTipBiznisa'], 'fk_Biznis_TipBiznisa1')->references(['idTipBiznisa'])->on('tipbiznisa')->onUpdate('NO ACTION')->onDelete('NO ACTION');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('biznis', function (Blueprint $table) {
            $table->dropForeign('fk_Biznis_Korisnik');
            $table->dropForeign('fk_Biznis_TipBiznisa1');
        });
    }
};
