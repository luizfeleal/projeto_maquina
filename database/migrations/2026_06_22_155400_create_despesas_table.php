<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDespesasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('despesas', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('id_cliente');
            $table->string('descricao');
            $table->decimal('valor', 10, 2);
            $table->date('data_despesa');
            $table->string('tipo')->nullable();
            $table->string('comprovante_path', 500)->nullable();
            $table->timestamps();

            $table->index('id_cliente');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('despesas');
    }
}
