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
        $id_prochain_joueur = $this->id_prochain_joueur;
        $prochainJoueur = Joueur::where('salon_id', $this->id)->where('id', '>', $id_prochain_joueur)->first();

        if ($prochainJoueur) {
            $this->id_prochain_joueur = $prochainJoueur->id;
        } else {
            $this->id_prochain_joueur = 0;
            $this->nextPlayer();
        }

        $this->save();
    }

    public function isFull() {
        return $this->nb_joueurs_max == $this->nb_joueurs_presents;
    }
}
