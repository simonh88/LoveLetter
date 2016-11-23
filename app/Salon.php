<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Salon extends Model
{
    protected $table = 'salons';
    protected $primaryKey = 'id';//Par défaut, pas besoin de le spécifier là

    public $timestamps = false;

    protected $fillable = ['nb_joueurs_max', 'nb_joueurs_presents', 'is_playing', 'no_manche'];


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


        if ($this->pioche_vide()) {
            /**
             * La pioche est vide, c'est la fin de la manche
             * On récupère le joueur qui possède la carte la plus grande
             * On lui ajoute un point
             * On démarre une nouvelle manche
             */
            Action::messageServeur($this, "Fin de la manche");
            Action::messageDebug($this, "La pioche est vide");
            $joueur_gagnant = $this->joueur_carte_superieur();
            $joueur_gagnant->ajouterPoint();
            $this->nouvelle_manche();

        } elseif (! $this->auMoinsDeuxJoueursNonElimines()) {
            /**
             * Il ne reste qu'un joueur non éliminé
             * On récupère ce joueur et on lui ajoute un point
             * On démarre une nouvelle manche
             */
            Action::messageServeur($this, "Fin de la manche");
            Action::messageDebug($this, "Il ne reste qu'un joueur");
            $joueur_gagnant = $this->dernierJoueurNonElimine();
            $joueur_gagnant->ajouterPoint();
            $this->nouvelle_manche();
        } else {
            // On passe au prochain joueur
            $id_prochain_joueur = $this->id_prochain_joueur;
            $prochainJoueur = Joueur::where('salon_id', $this->id)
                ->where('id', '>', $id_prochain_joueur)
                ->where('est_elimine', false)
                ->first();

            if ($prochainJoueur) {
                $this->id_prochain_joueur = $prochainJoueur->id;
            } else {
                Action::messageDebug($this, "Fin du tour, retour au premier joueur");
                $this->id_prochain_joueur = 0;
                $this->nextPlayer();
            }

            $this->save();
        }


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
     * @param $pioche La pioche à remplir
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

    /**
     * Créé un nouvelle manche si personne n'a gagné
     * Set l'id prochain joueur a 0
     * Reset pioche, défausses et mains
     * Reset l'elim des joueurs
     * Lance la partie
     */
    private function nouvelle_manche() {
        if (($j = $this->checkVictoire())) {
            Action::messageServeur($this, $j->username . " a gané la partie");
            // TODO fermer le salon
        } else {
            $this->id_prochain_joueur = 0;
            $this->no_manche = $this->no_manche + 1;
            $this->save();
            $this->vider_defausse();
            $this->vider_pioche();
            $this->supprimer_mains();
            $this->reset_elimination_joueur();
            self::init_pioche($this->getPioche());
            Action::messageServeur($this, "La manche n° " . $this->no_manche. " commence");
            $this->distribuerCartes();
            $this->nextPlayer();
        }
    }

    /**
     * A la fin d'une manche, on supprime les défausses
     */
    protected function vider_defausse() {
        $defausse = $this->getDefausse();
        CartesDansPile::where('pile_cartes_id', $defausse->id)->delete();
    }

    protected function vider_pioche() {
        $pioche = $this->getPioche();
        CartesDansPile::where('pile_cartes_id', $pioche->id)->delete();
    }

    /**
     * A la fin d'une manche, si il reste plus de deux joueurs
     * le gagnant est celui qui possède la carte d'une plus grande valeur
     */
    protected function joueur_carte_superieur() {
        $joueurs = $this->getJoueurs();
        $joueur_carte_max = null;
        $val_meilleure_carte = 0;
        foreach ($joueurs as $joueur) {
            $v = $joueur->valeurMeilleureCarte();
            if ($v > $val_meilleure_carte) {
                $joueur_carte_max = $joueur;
            }
        }
        return $joueur_carte_max;
    }

    /**
     * Utile quand on cherche le dernier joueur non éliminé
     * @return mixed un joueur non éliminé
     */
    protected function dernierJoueurNonElimine() {
        return Joueur::where('salon_id', $this->id)->where('est_elimine', false)->first();
    }

    /**
     * A la fin d'une manche, on supprime toutes les mains
     */
    protected function supprimer_mains() {
        $joueurs = $this->getJoueurs();
        foreach ($joueurs as $joueur) {
            $joueur->deleteMain();
        }
    }

    /**
     * Lors d'une nouvelle manche, les joueurs éliminés reviennent
     */
    protected function reset_elimination_joueur() {
        $joueurs = $this->getJoueurs();
        foreach ($joueurs as $joueur) {
            $joueur->est_elimine = false;
            $joueur->save();
        }
    }

    /**
     * Permet de vérifier si la pioche est vide
     * @return bool vrai si la pioche est vide
     */
    public function pioche_vide() {
        $pioche = $this->getPioche();
        $nbCartesDansPioche = CartesDansPile::where('pile_cartes_id', $pioche->id)->count();
        return $nbCartesDansPioche == 0;
    }

    public function commencer() {
        Action::messageServeur($this, "La partie commence");
        $this->is_playing = true;
        $this->save();
        $this->nouvelle_manche();
    }

    public function distribuerCartes() {
        Action::messageDebug($this, "Distribution des cartes");
        $joueurs = $this->getJoueurs();
        $pioche = $this->getPioche();

        foreach ($joueurs as $joueur){
            $carte = CartesDansPile::where('pile_cartes_id', $pioche->id)->inRandomOrder()->firstOrFail();
            var_dump($carte);
            Main::ajouterCarte($joueur->id, $carte->carte_id);
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

    /**
     * MAJ nb_joueurs_presents
     * Si il n'nen reste qu'un -> on lui demande de quitter
     * Si il en reste aucun -> on delete pioche + défausses + actions
     */
    public function maj() {
        $this->nb_joueurs_presents = Joueur::where('salon_id', $this->id)->count();
        if ($this->nb_joueurs_presents == 1) {
            $this->is_playing = false;
            $this->id_prochain_joueur = 0;
            Action::messageServeur($this, "Fin de la partie, veuillez quitter le salon");
        } else if ($this->nb_joueurs_presents == 0) {
            Action::where('salon_id', $this->id)->delete();
            $pioche = $this->getPioche();
            $defausse = $this->getDefausse();
            $this->no_manche = 0;
            CartesDansPile::where('pile_cartes_id', $pioche->id)->delete();
            CartesDansPile::where('pile_cartes_id', $defausse->id)->delete();
            self::init_pioche($pioche);

        }
        $this->save();
    }

    /**
     * @return bool vrai si il y a deux joueurs ou plus dans le salon
     */
    public function auMoinsDeuxJoueurs() {
        return Joueur::where('salon_id', $this->id)->count() >= 2;
    }

    /**
     * @return bool vrai si il reste plus de deux joueurs dans le salon non éliminés
     */
    public function auMoinsDeuxJoueursNonElimines() {

        $res =  Joueur::where('salon_id', $this->id)->where('est_elimine', false)->count() >= 2;
        Action::messageDebug($this, "auMoinsDeuxJoueursNonElimines ret " . $res);
        return $res;
    }

    /**
     * @return bool vrai si tous les joueurs présent dans le salon sont prêt
     */
    public function toutLeMondeEstPret() {
        $nbJoueurs = Joueur::where('salon_id', $this->id)->count();
        $nbJoueursPresents = Joueur::where('salon_id', $this->id)->where('is_ready', true)->count();
        return $nbJoueurs == $nbJoueursPresents;
    }

    /**
     * @param Joueur $joueur
     * @return Joueur Les joueurs appartenants au salon différent de $joueur
     */
    public function other_players(Joueur $joueur) {
        return Joueur::where('salon_id', $this->id)->where('id', '!=', $joueur->id)->get()->toArray();
    }

    /**
     * Reset le salon :
     * Delete tous les joueurs (donc toutes les mains)
     * Appel maj
     */
    public function reset() {
        $joueurs = Joueur::where('salon_id', $this->id)->cursor();
        foreach ($joueurs as $joueur) {
            $joueur->delete();
        }
        $this->maj();
    }

    public static function getSalonById($n) {
        return Salon::where('id', $n)->first();
    }

    protected function checkVictoire() {
        $joueurs = $this->getJoueurs();
        $points_pour_gagner = array(
            2 => 7,
            3 => 5,
            4 => 4,
        );

        $ppg = $points_pour_gagner[$this->nb_joueurs_presents];
        foreach ($joueurs as $joueur) {
            if ($joueur->nb_points == $ppg) {
                return $joueur;
            }
        }
        return false;
    }

}
