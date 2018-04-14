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
            			['categorias_id', '=',1],
        			])
            ->update(['listas_id' => 1]);

        DB::table('categorias_exercicios')
            ->where([
            			['exercicios_id', '>', 21],
                        ['exercicios_id', '<', 72],
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
                        ['exercicios_id', '<', 72],
            			['categorias_id', '=',2]
        			])
            ->update(['listas_id' => 4]);
        //Criando as relações de listas de dedução natural
        DB::table('categorias_exercicios')
            ->where([
                        ['exercicios_id', '<', 22],
                        ['categorias_id', '=',3]
                    ])
            ->update(['listas_id' => 5]);

        DB::table('categorias_exercicios')
            ->where([
                        ['exercicios_id', '>', 21],
                        ['exercicios_id', '<', 72],
                        ['categorias_id', '=',3]
                    ])
            ->update(['listas_id' => 6]);

        //Criando as relações de listas de tableaux de primeira ordem
        DB::table('categorias_exercicios')
            ->where([
                        ['exercicios_id', '>', 71],
                        ['exercicios_id', '<', 138],
                        ['categorias_id', '=',4]
                    ])
            ->update(['listas_id' => 7]);

        //Criando as relações de listas de dedução natural de primeira ordem
        DB::table('categorias_exercicios')
            ->where([
                        ['exercicios_id', '>', 71],
                        ['exercicios_id', '<', 138],
                        ['categorias_id', '=',5]
                    ])
            ->update(['listas_id' => 8]);
        //Criando as relações de listas de Semantica
        //Parte 1 da lista
        DB::table('categorias_exercicios')
            ->where([
                        ['exercicios_id', '>', 137],
                        ['exercicios_id', '<', 150],
                        ['categorias_id', '=',6]
                    ])
            ->update(['listas_id' => 9]);
        //Parte 2 da lista
        DB::table('categorias_exercicios')
            ->where([
                        ['exercicios_id', '>', 149],
                        ['exercicios_id', '<', 155],
                        ['categorias_id', '=',6]
                    ])
            ->update(['listas_id' => 10]);

    }
}
