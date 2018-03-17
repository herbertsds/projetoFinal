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
		    array('tipo'=>'resolucao'),
		    array('tipo'=>'tableaux')
		);

        App\Categorias::insert($dados);

        // Get all the roles attaching up to 3 random roles to each user
		$exercicios = App\Exercicios::all();

		// Populate the pivot table
		App\Categorias::all()->each(function ($categoria) use ($exercicios) { 
		    $categoria->exercicios()->attach($exercicios); 
		});
    }
}
