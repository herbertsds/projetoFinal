<?php

use Illuminate\Database\Seeder;

class listaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $dados = array(
		    array('nome'=>'Lista de DN sem Negação','categorias_id' => 1),
            array('nome'=>'Lista de DN com Negação','categorias_id' => 1),
            array('nome'=>'Lista de DN sem Negação','categorias_id' => 2),
            array('nome'=>'Lista de DN com Negação','categorias_id' => 2),
            array('nome'=>'Lista de DN sem Negação','categorias_id' => 3),
            array('nome'=>'Lista de DN com Negação','categorias_id' => 3),
            array('nome'=>'Lista de DN de Primeira Ordem','categorias_id' => 4),
            array('nome'=>'Lista de DN de Primeira Ordem','categorias_id' => 5),
            array('nome'=>'Semantica parte 1','categorias_id' => 6),
            array('nome'=>'Semantica parte 2','categorias_id' => 6)

		);

        App\Listas::insert($dados);
    }
}
