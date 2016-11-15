<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Salon extends Model
{
    protected $table = 'salons';
    protected $primaryKey = 'id';//Par défaut, pas besoin de le spécifier là

    public $timestamps = false;

    protected $fillable = ['nb_joueurs_max', 'nb_joueurs_presents', 'is_playing'];


    public function joueurs(){
        return $this->hasMany('App\Models\Joueur');
    }
    public function  cartesDansPile(){
        return $this->hasMany('App\Models\CartesDansPile');
    }

    public function nextPlayer() {
        $joueursDansSalon = Salon::where('salon_id', $this->id);

        // TODO chercher quel joueur n'a pas encore joué la manche et set id_prochain_joueur
    }
}
