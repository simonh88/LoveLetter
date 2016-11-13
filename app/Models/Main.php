<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Main extends Model
{
    protected $table='mains';
    protected $primaryKey='id';

  public function joueur(){
      return $this->belongsTo('App\Models\Joueur');
  }

  public function cartes(){
      return $this->belongsToMany('App\Models\Cartes');
  }
}
