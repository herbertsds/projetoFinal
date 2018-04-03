<?php

use Illuminate\Database\Seeder;

class categoriasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $dados = array(
		    array('tipo'=>'Resolução'),
		    array('tipo'=>'Tableaux')
		);

        App\Categorias::insert($dados);
        
    }
}
