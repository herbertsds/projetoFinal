<?php

use Illuminate\Database\Seeder;

class pivotSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Get all the roles attaching up to 3 random roles to each user
		$exercicios = App\Exercicios::all();

		// Populate the pivot table
		App\Categorias::all()->each(function ($categoria) use ($exercicios) { 
		    $categoria->exercicios()->attach($exercicios); 
		});

		//Criando as relações de listas de resolução
		DB::table('categorias_exercicios')
            ->where([
            			['exercicios_id', '<', 22],
            			['categorias_id', '=',1]
        			])
            ->update(['listas_id' => 1]);

        DB::table('categorias_exercicios')
            ->where([
            			['exercicios_id', '>', 21],
            			['categorias_id', '=',1]
        			])
            ->update(['listas_id' => 2]);

        //Criando as relações de listas de tableaux
        DB::table('categorias_exercicios')
            ->where([
            			['exercicios_id', '<', 22],
            			['categorias_id', '=',2]
        			])
            ->update(['listas_id' => 3]);

        DB::table('categorias_exercicios')
            ->where([
            			['exercicios_id', '>', 21],
            			['categorias_id', '=',2]
        			])
            ->update(['listas_id' => 4]);


    }
}
