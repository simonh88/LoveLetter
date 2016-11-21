<?php

namespace App;


use Illuminate\Database\Eloquent\Model;

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

    public function play($carte_id) {
        if (Main::where('carte_id', $carte_id)->where('joueur_id', $this->id)->count() == 0) {
            var_dump($carte_id);
            // TODO message lui disant qu'il ne possède pas cette carte
            return false;
        }
        Main::where('carte_id', $carte_id)->where('joueur_id', $this->id)->delete();
        $salon = $this->getSalon();
        $defausse = $salon->getDefausse();
        CartesDansPile::create([
           'carte_id' => $carte_id,
            'pile_cartes_id' => $defausse->id,
        ]);

        Action::joueurJoue($this, $carte_id);

        // TODO Si le joueur joue un handmaid, il est protégé

        // TODO Ne pas oublié d'enlever sa protection au prochain tour

        // TODO Si il joue un priest, il doit pouvoir choisir un joueur et voir sa main

        return true;
    }

    public function getMain() {
        $cartes = array();
        $cartesDansMain = Main::where('joueur_id', $this->id)->cursor();
        foreach ($cartesDansMain as $carteDansMain) {
            $carte = Cartes::where('id', $carteDansMain->carte_id)->firstOrFail();
            array_push($cartes, $carte);
        }

        // Si le joueur a une countess + prince ou king, il défausse la countess
        $this->checkCountessPrinceKing();

        return $cartes;
    }

    private function checkCountessPrinceKing() {
        // TODO A VERIFIER
        $cartesDansMain = Main::where('joueur_id', $this->id)->cursor();
        $countess = false;
        $princeOrKing = false;
        $idADetruire = -1;
        foreach($cartesDansMain  as $carteDansMain) {
            $carte = Cartes::where('id', $cartesDansMain->carte_id)->firstOrFail();
            if($carte->nom == 'Countess'){
                $countess = true;
                $idADetruire = $carteDansMain->id;
            } else if($carte->nom == 'King') $princeOrKing = true;
            else if($carte->nom == 'Prince') $princeOrKing = true;
        }
        if( $countess && $princeOrKing) Main::where('id', $idADetruire)->delete();
    }

    public static function getJoueurByUsername($username) {
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

}
