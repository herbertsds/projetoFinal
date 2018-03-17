<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CriarTabelaCategoriasExercicios extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('categorias_exercicios', function (Blueprint $table) {
            //Cria a relação de categorias no banco 
            $table->integer('categorias_id')->unsigned()->nullable();
            $table->foreign('categorias_id')->references('id')->on('categorias')->onDelete('cascade');

            //Cria a relação de exercícios no banco
            $table->integer('exercicios_id')->unsigned()->nullable();
            $table->foreign('exercicios_id')->references('id')->on('exercicios')->onDelete('cascade');

            $table->integer('dificuldade')->nullable();

            
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('categorias_exercicios');
    }
}
