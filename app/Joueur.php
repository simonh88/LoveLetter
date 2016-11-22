<?php

namespace App;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class Joueur extends Model
{
    protected $table = 'joueurs';
    protected $primaryKey = 'id';//Par défaut, pas besoin de le spécifier là

    protected $fillable = ['username', 'salon_id', 'est_protege', 'est_elimine', 'is_ready'];

    public $timestamps = false;

    public static function creerJoueur($username) {
        $joueur = Joueur::firstOrNew([
            'username' => $username,
            'salon_id' => null,
            'est_protege' => false,
            'est_elimine' => false,
            'is_ready' => false,
        ]);
        $joueur->save();
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
        $salon = $this->getSalon();
        $salon->nb_joueurs_presents ++;
        $salon->save();
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
        $nbCartes = Main::where('joueur_id', $this->id)->count();
        return $nbCartes == 2;
    }

    public function possedeCarte($carte_id){
        if (Main::where('carte_id', $carte_id)->where('joueur_id', $this->id)->count() == 0) {
            return false;
        }
        return true;
    }

    public function play($carte_id) {
        //On reset a chaque début de tour est_protege à false
        $this->est_protege = false;
        $this->save();

        $this->deleteCard($carte_id);

        Action::joueurJoue($this, $carte_id);

        // TODO Si il joue un priest, il doit pouvoir choisir un joueur et voir sa main

        return true;
    }

    /**
     * Fonction checkant si le joueur joue un handmaid
     * et lui met une protectection si oui
     */
    public static function handmaidJoue(){
        $joueur = Joueur::getJoueurByUsername(Auth::user()->name);
        $joueur->est_protege = true;
        $joueur->save();
        $msg = $joueur->username . " est immunisé pendant un tour";
        Action::messageServeur($joueur->getSalon(), $msg);
    }

    public function getMain() {
        $cartes = array();
        $cartesDansMain = Main::where('joueur_id', $this->id)->cursor();
        foreach ($cartesDansMain as $carteDansMain) {
            $carte = Cartes::where('id', $carteDansMain->carte_id)->firstOrFail();
            array_push($cartes, $carte);
        }

        // Si le joueur a une countess + prince ou king, il défausse la countess
        //$this->checkCountessPrinceKing();

        return $cartes;
    }

    /*private function checkCountessPrinceKing() {
        $cartesDansMain = Main::where('joueur_id', $this->id)->cursor();
        $countess = false;
        $princeOrKing = false;

        foreach($cartesDansMain  as $carteDansMain) {
            $carte = Cartes::where('id', $carteDansMain->carte_id)->firstOrFail();
            if($carte->nom == 'Countess'){
                $countess = true;
                $carteASuppr = $carteDansMain;
            } else if($carte->nom == 'King') $princeOrKing = true;
            else if($carte->nom == 'Prince') $princeOrKing = true;
        }
        if( $countess && $princeOrKing){
            $this->deleteCard($carteASuppr->carte_id);
        }
    }*/

    private function deleteCard($carte_id){
        Main::supprimerCarte($this->id, $carte_id);
        $pileCartes = $this->getSalon()->getDefausse();
        CartesDansPile::create([
            'carte_id' => $carte_id,
            'pile_cartes_id' => $pileCartes->id,
            'joueur_id' => $this->id
        ]);
    }

    public static function getJoueurByUsername() {
        $username = Auth::user()->name;
        if(empty(Joueur::where('username', $username)->first())){
            self::creerJoueur($username);
        }
        return Joueur::where('username', $username)->firstOrFail();
    }

    public function quitterSalon() {
        $salon = $this->getSalon();
        Action::messageServeur($salon, $this->username . " a quitté le salon");
        Main::where('joueur_id', $this->id)->delete();
        Joueur::where('id', $this->id)->delete();
        $salon->maj();
    }

    public function ready() {
        $this->is_ready = true;
        $this->save();
        $salon = $this->getSalon();
        if ($salon->auMoinsDeuxJoueurs() && $salon->toutLeMondeEstPret()) {
            $salon->commencer();
        }
    }

    public function dansAucunSalon() {
        return $this->salon_id == null;
    }

    public function delete() {
        Main::where('joueur_id', $this->id)->delete();
        Joueur::where('id', $this->id)->delete();
    }

    public static function elimine(){
        $joueur = self::getJoueurByUsername();
        $joueur->est_elimine = true;
        $joueur->save();
        //On delete toute ses cartes
        $cartesDansMain = Main::where('joueur_id', $joueur->id)->cursor();

        foreach ($cartesDansMain as $carteMain){
            $joueur->deleteCard($carteMain->carte_id);
        }

        $salon = $joueur->getSalon();
        $msg = $joueur->username . " est éliminé, il a défausser la carte Princess";
        Action::messageServeur($salon, $msg);
        //TODO VERIFIER SIL NE RESTE QU'UN JOUEUR DU COUP PARTIE TERMINEE
        // TODO SINON ON FAIT UN NEXTTURN
    }

    public function isReady() {
        return $this->is_ready;
    }

    public static function getJoueurConnecte() {
        return Joueur::getJoueurByUsername(Auth::user()->name);
    }

}
