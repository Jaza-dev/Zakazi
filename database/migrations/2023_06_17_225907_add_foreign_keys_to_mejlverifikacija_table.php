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
        Schema::table('mejlverifikacija', function (Blueprint $table) {
            $table->foreign(['Korisnik_idKor'], 'fk_MejlVerifikacija_Korisnik1')->references(['idKor'])->on('korisnik')->onUpdate('NO ACTION')->onDelete('NO ACTION');
            $table->foreign(['Zaposlenje_idZaposlenja'], 'fk_MejlVerifikacija_Zaposlenje1')->references(['idZaposlenja'])->on('zaposlenje')->onUpdate('NO ACTION')->onDelete('NO ACTION');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('mejlverifikacija', function (Blueprint $table) {
            $table->dropForeign('fk_MejlVerifikacija_Korisnik1');
            $table->dropForeign('fk_MejlVerifikacija_Zaposlenje1');
        });
    }
};
