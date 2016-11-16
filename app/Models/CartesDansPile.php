<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CartesDansPile extends Model
{
    protected $table = 'cartes_dans_pile';
    protected $primaryKey = 'id';//Par défaut, pas besoin de le spécifier là
    public $timestamps = false;

    public function pileCartes(){
        return $this->belongsTo('App\Models\CartesDansPile');
    }

    public function cartes(){
        return $this->belongsToMany('App\Model\Cartes');
    }
}
