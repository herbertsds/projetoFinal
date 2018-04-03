<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Categorias extends Model
{
    public function exercicios(){
    	return $this->belongsToMany('App\Exercicios');
    }

    public function listas(){
    	return $this->hasMany('App\Listas');
    }

    public static function condicao($coluna, $valor){
    	return Categorias::find(Categorias::where($coluna,$valor)->value('id'));
    }

}
