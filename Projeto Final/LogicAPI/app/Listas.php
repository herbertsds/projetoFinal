<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Listas extends Model
{
    public function exercicios(){
    	return $this->belongsToMany('App\Exercicios', 'categorias_exercicios');
    }

    public function categorias(){
    	return $this->hasOne('App\Categorias');
    }
}
