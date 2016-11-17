<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Main extends Model
{
    protected $table='mains';
    protected $primaryKey='id';
    protected $fillable = ['joueur_id', 'carte_id'];
    public $timestamps = false;

  public function joueur(){
      return $this->belongsTo('App\Models\Joueur');
  }

  public function cartes(){
      return $this->belongsToMany('App\Models\Cartes');
  }

  public static function ajouterCarte($idJoueur, $idCarte) {
      Main::create([
          'joueur_id' => $idJoueur,
          'carte_id' => $idCarte
      ]);

  }
}
