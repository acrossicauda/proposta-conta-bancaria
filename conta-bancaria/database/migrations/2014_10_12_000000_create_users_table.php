<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //“CodigoCliente”, “Ativa“ e “LimiteDisponivel“ –
        Schema::create('users', function (Blueprint $table) {
            $table->id('CodigoCliente');
            $table->string('name');
            $table->string('api_token');
            $table->string('password');
            $table->integer('Ativa');
            $table->double('LimiteDisponivel');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
