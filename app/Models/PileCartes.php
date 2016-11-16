<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PileCartes extends Model
{
    protected $table = 'piles_cartes';
    protected $primaryKey = 'id';//Par défaut, pas besoin de le spécifier là

    public $timestamps = false;

    protected $fillable = ['salon_id'];

    public function salon(){
        return $this->belongsTo('App\Models\Salon');
    }

    public function cartesDansPile(){
        return $this->hasMany('App\Models\CartesDansPile');
    }
}
