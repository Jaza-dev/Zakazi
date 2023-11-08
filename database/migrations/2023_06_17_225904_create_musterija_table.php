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
        Schema::create('musterija', function (Blueprint $table) {
            $table->integer('Korisnik_idKor')->primary();
            $table->string('korisnickoIme', 45);
            $table->string('ime', 45);
            $table->string('prezime', 45);
            $table->string('noviEmail', 45)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('musterija');
    }
};
