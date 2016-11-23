<?php
/**
 * Created by PhpStorm.
 * User: guillaumepetit
 * Date: 14/11/2016
 * Time: 22:15
 */

namespace App\Http\Controllers;

use App\Action;
use App\Cartes;
use Illuminate\Http\Request;
use App\Joueur;
use App\Salon;
use App\Main;
use Illuminate\Support\Facades\Auth;


class JeuxController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function myturn() {

        $joueur = Joueur::getJoueurConnecte();
        $salon = $joueur->getSalon();

        $res = array();


        if ($joueur->checkTurn()) {
            //echo("C'est mon tour");

            if (!$joueur->aPioche()) {
                $joueur->piocherCarte();
            }

            $res['myturn'] = true;

        } else {

            $res['myturn'] = false;

        }

        $res['username'] = $joueur->username;
        $res['main'] = $joueur->getMain();
        $res['actions'] = $joueur->getSalon()->getActions();
        $res['other_players'] = $salon->other_players($joueur);

        return json_encode($res);
    }

    public function play($carte_id) {
        $joueur = Joueur::getJoueurConnecte();
        if ($joueur->possedeCarte($carte_id)) {
            //Si verifPrincess est vrai, on ne fait rien, le joueur se fait eliminer
            if(!$this->verifPrincess($carte_id, $joueur)) {
                $joueur->endTurn();
                $joueur->play($carte_id);
                $this->verifHandmaid($carte_id, $joueur);
            }
        }
    }

    public function playCible($carte_id, $joueur_cibleUsername){
        //TODO action sur la cible mais sans carte a deviner
        //KingPrinceBaronPriest
        $carte = Cartes::where('id', $carte_id)->first();
        $joueur = Joueur::getJoueurConnecte();
        $joueur->endTurn();
        $joueur->play($carte_id);
        $joueur_cible = Joueur::getJoueurByUsername($joueur_cibleUsername);

        switch($carte->nom){
            case "King":
                $joueur->kingEffect($joueur_cible->id);
                break;
            case "Prince":
                //Qu'une carte dans la main au moment là normalement
                $mainCible = Main::where('joueur_id', $joueur_cible->id)->firstOrFail();
                if(!$this->verifPrincess($mainCible->carte_id, $joueur_cible)){
                    $joueur_cible->princeEffect();
                }
                break;
            case "Baron":
                $joueur->baronEffect($joueur_cible->id);
                break;
            case "Priest":
                $joueur->priestEffect($joueur_cible);
                break;
        }

    }

    public function playCibleCarte($carte_id, $joueur_cible, $carte_devine){
        //TODO action sur la cible + devine sa carte
        $this->play($carte_id);

        $joueur = Joueur::getJoueurConnecte();
        $joueur->guardEffect($joueur_cible, $carte_devine);
        $msg = $joueur->username .  " et " . $joueur_cible;
        Action::messageServeur($joueur->getSalon(), $msg);
    }

    public function chat($msg) {
        Action::joueurChat(Joueur::getJoueurConnecte(), $msg);
    }

    public function quit() {
        $joueur = Joueur::getJoueurConnecte();
        $joueur->quitterSalon();
        return redirect("/");
    }

    public function ready() {
        $joueur = Joueur::getJoueurConnecte();
        $res = array();
        if (!$joueur->isReady())
        {
            Action::messageServeur($joueur->getSalon(), $joueur->username . " est prêt");
            $joueur->ready();
            $res['success'] = 'OK';
        } else {
            $res['success'] = 'FAIL';
            $res['message'] = 'Le joueur ' . $joueur->username . " est deja pret";
        }
        return json_encode($res);
    }

    public function clearAllSalons() {
        $salons = Salon::all();
        foreach ($salons as $salon) {
            $salon->reset();
        }
        return redirect('/jouer');
    }


    /***************VERIFS REGLES*****************/
    private function verifPrincess($carte_id, $joueur){
        $carte = Cartes::where('id', $carte_id)->firstOrFail();
        if($carte->nom == 'Princess'){
            $msg = $joueur->username . " a été éliminé car il a défausser la princesse";
            Action::messageServeur($joueur->getSalon(), $msg);
            $joueur->elimine();
            return true;
        }
        return false;
    }

    private function verifHandmaid($carte_id, $joueur){
        $carte = Cartes::where('id', $carte_id)->firstOrFail();
        if($carte->nom == "Handmaid"){
            $joueur->handmaidJoue();
        }
    }
}