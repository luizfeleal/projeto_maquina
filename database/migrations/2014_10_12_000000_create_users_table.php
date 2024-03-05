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
        Schema::create('usuarios', function (Blueprint $table) {
            $table->id('id_usuario');
            $table->int('id_grupo_acesso');
            $table->int('id_cliente');
            $table->string('usuario_nome');
            $table->string('usuario_email')->unique();
            $table->timestamp('usuario_ultimo_acesso')->nullable();
            $table->string('usuario_tipo');
            $table->string('usuario_login');
            $table->string('usuario_senha');
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
        Schema::dropIfExists('usuarios');
    }
}
