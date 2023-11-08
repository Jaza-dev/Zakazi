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
        Schema::create('mejlverifikacija', function (Blueprint $table) {
            $table->string('hash', 32)->unique('hash_UNIQUE');
            $table->integer('Korisnik_idKor')->nullable()->index('fk_MejlVerifikacija_Korisnik1_idx');
            $table->integer('Zaposlenje_idZaposlenja')->nullable()->index('fk_MejlVerifikacija_Zaposlenje1_idx');
            $table->string('email', 45)->nullable();

            $table->primary(['hash']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mejlverifikacija');
    }
};
