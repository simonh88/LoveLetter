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


class JeuxController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function myturn(Request $req) {

        $username = $req->session()->get('username');
        $joueur = Joueur::where('username', $username)->firstOrFail();

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

        return json_encode($res);
    }

    public function play(Request $req, $carte_id) {
        $username = $req->session()->get('username');
        $joueur = Joueur::where('username', $username)->firstOrFail();
        $salon = $joueur->getSalon();
        if ($joueur->play($carte_id))
        {
            $joueur->endTurn();
        }
    }

    public function chat(Request $req, $msg) {
        $username = $req->session()->get('username');
        $joueur = Joueur::where('username', $username)->firstOrFail();
        $salon = $joueur->getSalon();
        Action::joueurChat($joueur, $msg);
    }

    public function quit(Request $req) {
        $username = $req->session()->get('username');
        $joueur = Joueur::getJoueurByUsername($username);
        $joueur->quitterSalon();
        return redirect("/");
    }

    public function ready(Request $req) {
        $username = $req->session()->get('username');
        $joueur = Joueur::getJoueurByUsername($username);
        Action::messageServeur($joueur->getSalon(), $username . " est prêt");
        $joueur->ready();
    }

}