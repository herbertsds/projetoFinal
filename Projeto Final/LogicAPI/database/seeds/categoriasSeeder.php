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
		    array('tipo'=>'Tableaux'),
            array('tipo'=>'Dedução_Natural'),
            array('tipo'=>'LPO_Tableaux'),
            array('tipo'=>'LPO_Dedução_Natural'),
            array('tipo'=>'Semantica')
		);

        App\Categorias::insert($dados);
        
    }
}
