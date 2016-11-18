<?php
/**
 * Created by PhpStorm.
 * User: guillaumepetit
 * Date: 14/11/2016
 * Time: 22:15
 */

namespace App\Http\Controllers;

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

        // TODO, en plus de lui renvoyé la carte qu'il a pioché, on peut lui renvoyé toute sa main
        // TODO ça permettrait au mec de se resynchro avec le serveur à cas ou il quitte la page ou autre
        // TODO + si il essaie de cheat en modifiant le JS

        // De toute façon on peut blinder le json

        // TODO Table actions qui stock tout ce qui se passe dans un salon -> on balance ça dans le json aussi


        // TODO Faire en sorte que si le joueur appelle myturn deux fois il ne pioche pas deux fois -> nouvel attribut dans la DB ?

        // Si le joueur a déjà pioché, on set $turn à faux
        // A la fin d'un tour, on set aPioché à faux pour tous les joueurs
        // ensuite on pourra supprimer ismyturn dans le script js

        /**
         * Dans res on met -> la main actuel du joueur
         *                 -> les actions qu'il veut
         *
         * -> Mettre en place un myturn/{numAction}
         */

        $res = array();


        if ($joueur->checkTurn()) {


            if (!$joueur->aPioche()) {
                $joueur->piocherCarte();
            }

            $res['myturn'] = true;

        } else {

            $res['myturn'] = false;

        }

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

    public function testdistrib(Request $req, $n) {
        $salon = Salon::where('id', $n)->firstOrFail();
        $salon->distribuerCartes();
        return 'OK';
    }
}