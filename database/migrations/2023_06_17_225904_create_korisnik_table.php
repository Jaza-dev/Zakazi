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
        Schema::create('korisnik', function (Blueprint $table) {
            $table->integer('idKor', true)->unique('idKor_UNIQUE');
            $table->string('email', 45)->unique('email_UNIQUE');
            $table->string('lozinka', 45);
            $table->tinyInteger('aktivan');
            $table->integer('tipKorisnika');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('korisnik');
    }
};
