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
        return Salon::where('id', $this->salon_id)->first();
    }

    public function piocherCarte() {
        $salon = $this->getSalon();

        $pioche = PileCartes::where('salon_id', $salon->id)->where('estPioche', true)->first();

        $carteID = CartesDansPile::where('pile_cartes_id', $pioche->id)->inRandomOrder()->first();
        $carte = Cartes::where('id', $carteID->carte_id)->first();



        CartesDansPile::destroy($carteID->id);

        Main::ajouterCarte($this->id, $carteID->carte_id);
        return $carte;
    }

    public function aPioche() {
        $nbCartes = Mains::where('joueur_id', $this->id)->count();
        return $nbCartes == 2;
    }

    public function play($carte_id) {
        // TODO faire la table action
        if (Main::where('carte_id', $carte_id)->where('joueur_id', $this->id)->count() == 0) {
            // TODO message lui disant qu'il ne possède pas cette carte
            return false;
        }
        // TODO Ajouter une action
        Main::where('carte_id', $carte_id)->where('joueur_id', $this->id)->delete();
        $salon = $this->getSalon();
        $defausse = $salon->getDefausse();
        CartesDansPile::create([
           'carte_id' => $carte_id,
            'pile_cartes_id' => $defausse->id,
        ]);

        return true;
    }

}
