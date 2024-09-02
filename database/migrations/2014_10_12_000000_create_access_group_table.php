<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAccessGroupTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('grupos_acesso', function (Blueprint $table) {
            $table->id('id_grupo_acesso');
            $table->int('id_usuario');
            $table->string('grupo_acesso_nome');
            $table->timestamps('data_criacao');
            $table->timestamps('data_alteracao');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('grupos_acesso');
    }
}
