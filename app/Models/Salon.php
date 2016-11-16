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

    /**
     * Fin du tour du jour courant et début du tour du prochain joueur
     */
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

    /**
     * @return bool Indique si le salon est plein
     */
    public function isFull() {
        return $this->nb_joueurs_max == $this->nb_joueurs_presents;
    }

    /**
     * recherche d'un salon libre, s'il n'y en a pas, on en crée un
     * @return mixed
     */
    public static function chercherSalon(){
        $salon = Salon::where('is_playing', false)->whereColumn('nb_joueurs_presents', '<', 'nb_joueurs_max')->first();

        if(empty($salon)){
            $salon = Salon::creationSalon(4);
        }

        $salon->nb_joueurs_presents += 1;
        $salon->save();

        if ($salon->isFull()) {
            $salon->nextPlayer();
        }

        return $salon->id;
    }

    /**
     * Creation d'un salon
     */
    public static function creationSalon($nbJoueurMax){
        $salon =  Salon::create([
            'nb_joueurs_max' => $nbJoueurMax,
            'nb_joueurs_presents' => 0,
            'is_playing' => false
        ]);

        $pioche = PileCartes::create([
            'salon_id' => $salon->id,
            'estPioche' => true,
        ]);

        $pioche->save();

        $defausse = PileCartes::create([
            'salon_id' => $salon->id,
            'estPioche' => false,
        ]);

        $defausse->save();

        self::init_pioche($pioche);
        return $salon;
    }

    /**
     * Permet de mélanger les cartes et de remplir la pioche
     * @param La pioche à remplir
     */
    protected static function init_pioche($pioche) {

        $cartes = Cartes::all();
        foreach ($cartes as $carte) {
            CartesDansPile::create([
                'carte_id' => $carte->id,
                'pile_cartes_id' => $pioche->id,
            ]);
        }
    }

    public function distribuerCartes() {
        // TODO première distribution en début de partie
    }
}
