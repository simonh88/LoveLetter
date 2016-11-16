<?php

namespace App;


use Illuminate\Database\Eloquent\Model;

class Joueur extends Model
{
    protected $table = 'joueurs';
    protected $primaryKey = 'id';//Par défaut, pas besoin de le spécifier là

    protected $fillable = ['username', 'salon_id'];

    public $timestamps = false;

    public static function creerJoueur($username) {
        $joueur = Joueur::firstOrNew(['username' => $username]);
        return $joueur;
    }

    public function main(){
        return $this->hasMany('App\Models\Main');
    }

    public function salon(){
        return $this->belongsTo('App\Models\Salon');
    }

    public function checkTurn() {
        $salon = Salon::where('id', $this->salon_id)->firstOrFail();
        return $salon->id_prochain_joueur == $this->id;
    }

    public function endTurn() {
        $salon = Salon::where('id', $this->salon_id)->firstOrFail();
        $salon->nextPlayer();
    }

    public function setSalon($idSalon) {
        $this->salon_id = $idSalon;
        $this->is_ready = 1;
        $this->save();
    }

    public function getSalon() {
        return Salon::where('id', $this->salon_id);
    }

    public function piocherCarte() {
        $salon = $this->getSalon();
        $pioche = PileCartes::where('salon_id', $salon->id)->where('estPioche', true);
        $carte = CartesDansPile::where('pile_cartes_id', $pioche->id)->inRandomOrder()->first();
        CartesDansPile::destroy($carte->id);
        return $carte;
    }
}
