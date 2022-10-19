<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\User;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('denuncias', function (Blueprint $table) {
            $table->unsignedBigInteger('idUsuario');
            $table->id();
            $table->string('tipo');
            $table->string('cor');
            $table->string('localizacao');
            $table->string('rua');
            $table->string('bairro');
            $table->string('pontoDeReferencia');
            $table->string('picture');
            $table->timestamps();
        });

        Schema::table('denuncias', function (Blueprint $table) {
            $table->foreign('idUsuario')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('denuncias');
    }
};
