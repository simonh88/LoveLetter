<?php

namespace App;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

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
        return $this->hasMany('App\Main');
    }

    public function salon(){
        return $this->belongsTo('App\Salon');
    }

    public function checkTurn() {
        $salon = Salon::where('id', $this->salon_id)->firstOrFail();
        return $salon->id_prochain_joueur == $this->id;
    }

    public function endTurn() {
        $salon = $this->getSalon();
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
    public function handmaidJoue(){
        $this->est_protege = true;
        $this->save();
        $msg = $this->username . " est immunisé pendant un tour";
        Action::messageServeur($this->getSalon(), $msg);
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

    private function deleteCard($carte_id){
        Main::supprimerCarte($this->id, $carte_id);
        $pileCartes = $this->getSalon()->getDefausse();
        CartesDansPile::create([
            'carte_id' => $carte_id,
            'pile_cartes_id' => $pileCartes->id,
            'joueur_id' => $this->id
        ]);
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
        if ($salon->isFull() && $salon->toutLeMondeEstPret()) {
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


    public function elimine(){
        $joueur = self::getJoueurConnecte();
        $joueur->est_elimine = true;
        $joueur->save();
        //On delete toute ses cartes
        $cartesDansMain = Main::where('joueur_id', $joueur->id)->cursor();

        foreach ($cartesDansMain as $carteMain){
            $joueur->deleteCard($carteMain->carte_id);
        }

        $salon = $joueur->getSalon();
        //TODO VERIFIER SIL NE RESTE QU'UN JOUEUR DU COUP PARTIE TERMINEE
        // TODO SINON ON FAIT UN NEXTTURN
    }

    public function isReady() {
        return $this->is_ready;
    }

    public static function getJoueurConnecte() {
        $username = Auth::user()->name;
        if(empty(Joueur::where('username', $username)->first())){
            self::creerJoueur($username);
        }
        return Joueur::where('username', $username)->firstOrFail();
    }

    public function deleteMain() {
        Main::where('joueur_id', $this->id)->delete();
    }

    /**
     * @return mixed la valeur de sa meilleure carte, utile à la fin d'une manche
     */
    public function valeurMeilleureCarte() {
        $valMeilleure = 0;
        foreach ($this->main()->cursor() as $carteDansMain) {
            $v = Cartes::where('id', $carteDansMain->carte_id)->orderBy('valeur')->first()->valeur;
            if ($v > $valMeilleure) {
                $valMeilleure = $v;
            }
        }
        return $valMeilleure;
    }

    /**
     * Ajoute un point au joueur
     */
    public function ajouterPoint() {
        $this->nb_points = $this->nb_points + 1;
        $this->save();
        Action::messageServeur($this->getSalon(), $this->username . " a gagné un point");
    }

    public function kingEffect($id_joueur_cible){
        //TODO MESSAGE
        //Normalement ils n'ont qu'une carte chacun au moment là
        $mainAdverse = Main::where('joueur_id', $id_joueur_cible)->firstOrFail();
        $main = Main::where('joueur_id', $this->id)->firstOrFail();
        Main::ajouterCarte($id_joueur_cible, $main->carte_id);
        Main::ajouterCarte($this->id, $mainAdverse->carte_id);
        Main::supprimerCarte($id_joueur_cible, $mainAdverse->carte_id);
        Main::supprimerCarte($this->id, $main->carte_id);
        $msg = "Echange de cartes car king joué";
        Action::messageServeur($this->getSalon(), $msg);
    }

    public function princeEffect(){
        // TODO MESSAGE
        $main = Main::where('joueur_id', $this->id)->firstOrFail();
        $debug = " id de la carte detruite " . $main->carte_id;
        $this->deleteCard($main->carte_id);
        Action::messageDebug($this->getSalon(), $debug);
        $this->piocherCarte();
        $msg = "Defausse puis repioche suite a prince joué ";
        Action::messageServeur($this->getSalon(), $msg);
    }

    public function baronEffect($id_joueur_cible){
        // TODO MESSAGE
        $main = Main::where('joueur_id', $this->id)->firstOrFail();
        $mainAdverse = Main::where('joueur_id', $id_joueur_cible)->firstOrFail();
        $carte = Cartes::where('id', $main->carte_id)->firstOrFail();
        $carteAdverse = Cartes::where('id', $mainAdverse->carte_id)->firstOrFail();
        $joueurCible = Joueur::where('id', $id_joueur_cible)->firstOrFail();
        $msgDebug = $carte->nom . " " . $carte->valeur . " comparaison ". $carteAdverse->nom . " " . $carteAdverse->valeur;
        Action::messageDebug($joueurCible->getSalon(), $msgDebug);
        if($carte->valeur > $carteAdverse->valeur){
            //joueur_cible qui se fait eliminé
            $joueurCible->elimine();
            $msg =  $joueurCible->username . " a été éliminé cause comparaison Baron avec " . $this->username;
            Action::messageServeur($this->getSalon(), $msg);

        }elseif($carte->valeur < $carteAdverse->valeur){
            //joueur qui se fait eliminé
            $this->elimine();
            $msg = $this->username . " a été éliminé cause comparaison Baron avec " .$joueurCible->username;
            Action::messageServeur($this->getSalon(), $msg);

        }else{
            //=
            $msg = $this->username . " et " . $joueurCible->username . " avaient des cartes de même valeur(comp Baron)";
            Action::messageServeur($this->getSalon(), $msg);
        }

    }

    public function priestEffect($joueurCible){

    }

    public function guardEffect($joueurCibleUsername, $carte_devine){
        $joueurCible = self::getJoueurByUsername($joueurCibleUsername);
        $mainAdverse = Main::where('joueur_id', $joueurCible->id)->firstOrFail();
        $carteAdverse = Cartes::where('id', $mainAdverse->carte_id)->firstOrFail();
        //DEBUG
        $msg1 = $carteAdverse->nom . " == " . $carte_devine;
        Action::messageServeur($this->getSalon(),  $msg1);
        if($carteAdverse->nom == $carte_devine){
            $joueurCible->elimine();
            $msg = $joueurCible->username . " a été éliminé car guard et trouvé bonne carte";
            Action::messageServeur($this->getSalon(), $msg);
        }else{
            $msg = "guard joué mais mauvaise carte proposée donc sans effets";
            Action::messageServeur($this->getSalon(), $msg);
        }
    }

}
