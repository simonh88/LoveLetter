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
    public function actions(){
        return $this->hasOne('App\Models\Action');
    }

    /**
     * Fin du tour du jour courant et début du tour du prochain joueur
     */
    public function nextPlayer() {
        $id_prochain_joueur = $this->id_prochain_joueur;
        $prochainJoueur = Joueur::where('salon_id', $this->id)
            ->where('id', '>', $id_prochain_joueur)
            ->where('est_elimine', false)
            ->first();

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

        return $salon;
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

        $salon->save();

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

    public function commencer() {
        Action::messageServeur($this, "La partie commence");
        $this->is_playing = true;
        $this->save();
        $this->distribuerCartes();
        $this->nextPlayer();
    }

    public function distribuerCartes() {
        $joueurs = $this->getJoueurs();
        $pioche = $this->getPioche();

        foreach ($joueurs as $joueur){
            $carte = CartesDansPile::where('pile_cartes_id', $pioche->id)->inRandomOrder()->firstOrFail();
            Main::ajouterCarte($joueur->id, $carte->id);
            CartesDansPile::destroy($carte->id);
            CartesDansPile::where('pile_cartes_id', $pioche->id)->where('carte_id', $carte->id)->delete();
        }
    }

    public function getDefausse() {
        return PileCartes::where('salon_id', $this->id)->where('estPioche', false)->firstOrFail();
    }

    public function getPioche() {
        return PileCartes::where('salon_id', $this->id)->where('estPioche', true)->firstOrFail();
    }

    public function getJoueurs() {
        return Joueur::where('salon_id', $this->id)->cursor();
    }

    public function getActions() {
        return Action::where('salon_id', $this->id)->get()->toArray();
    }

    public function maj() {
        $this->nb_joueurs_presents = Joueur::where('salon_id', $this->id)->count();
        if ($this->nb_joueurs_presents == 0) {
            $this->is_playing = false;
            $this->id_prochain_joueur = 0;
            Action::where('salon_id', $this->id)->delete();
        }
        $this->save();
    }

    public function auMoinsDeuxJoueurs() {
        return Joueur::where('salon_id', $this->id)->count() >= 2;
    }

    public function toutLeMondeEstPret() {
        $nbJoueurs = Joueur::where('salon_id', $this->id)->count();
        $nbJoueursPresents = Joueur::where('salon_id', $this->id)->where('is_ready', true)->count();
        return $nbJoueurs == $nbJoueursPresents;
    }
}
