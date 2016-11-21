<?php
/**
 * Created by PhpStorm.
 * User: guillaumepetit
 * Date: 14/11/2016
 * Time: 22:15
 */

namespace App\Http\Controllers;

use App\Action;
use Illuminate\Http\Request;
use App\Joueur;
use App\Salon;
use Illuminate\Support\Facades\Auth;


class JeuxController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function myturn() {

        $username = Auth::user()->name;
        $joueur = Joueur::getJoueurByUsername($username);
        $salon = $joueur->getSalon();

        $res = array();


        if ($joueur->checkTurn()) {


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
        $username = Auth::user()->name;
        $joueur = Joueur::where('username', $username)->firstOrFail();
        if ($joueur->possedeCarte($carte_id))
        {
            $joueur->play($carte_id);
            $joueur->endTurn();
        }
    }

    public function playCible($carte_id, $joueur_cible){
        //TODO action sur la cible mais sans carte a deviner
        $username = Auth::user()->name;
        $joueur = Joueur::where('username', $username)->firstOrFail();
        $salon = $joueur->getSalon();
        if ($joueur->play($carte_id)) {
            $joueur->endTurn();
        }

    }

    public function playCibleCarte($carte_id, $joueur_cible, $carte_devine){
        //TODO action sur la cible + devine sa carte
        $username = Auth::user()->name;
        $joueur = Joueur::where('username', $username)->firstOrFail();
        if ($joueur->play($carte_id)) {
            $joueur->endTurn();
        }
    }

    public function chat($msg) {
        $username = Auth::user()->name;
        $joueur = Joueur::where('username', $username)->firstOrFail();
        Action::joueurChat($joueur, $msg);
    }

    public function quit() {
        $username = Auth::user()->name;
        $joueur = Joueur::getJoueurByUsername($username);
        $joueur->quitterSalon();
        return redirect("/");
    }

    public function ready() {
        $username = Auth::user()->name;
        $joueur = Joueur::getJoueurByUsername($username);
        Action::messageServeur($joueur->getSalon(), $username . " est prêt");
        $joueur->ready();
    }

    public function clearAllSalons() {
        $salons = Salon::all();
        foreach ($salons as $salon) {
            $salon->reset();
        }
        return redirect('/jouer');
    }

}