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
		);

        App\Listas::insert($dados);
    }
}
