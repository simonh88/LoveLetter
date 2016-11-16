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
    public function myturn(Request $req) {

        $username = $req->session()->get('username');
        $joueur = Joueur::where('username', $username)->firstOrFail();
        $turn = $joueur->checkTurn();

        // TODO, en plus de lui renvoyé la carte qu'il a pioché, on peut lui renvoyé toute sa main
        // TODO ça permettrait au mec de se resynchro avec le serveur à cas ou il quitte la page ou autre
        // TODO + si il essaie de cheat en modifiant le JS

        // De toute façon on peut blinder le json

        // TODO Table actions qui stock tout ce qui se passe dans un salon -> on balance ça dans le json aussi


        // TODO Faire en sorte que si le joueur appelle myturn deux fois il ne pioche pas deux fois -> nouvel attribut dans la DB ?

        // Si le joueur a déjà pioché, on set $turn à faux
        // A la fin d'un tour, on set aPioché à faux pour tous les joueurs
        // ensuite on pourra supprimer ismyturn dans le script js

        //if ($joueur->aPioche()) $turn = false;

        $res = array('myturn' => $turn);
        if ($turn) {
                $res['card'] = $joueur->piocherCarte()->valeur;
        }

        return json_encode($res);
    }

    public function play(Request $req, $card) {
        $username = $req->session()->get('username');
        $joueur = Joueur::where('username', $username)->firstOrFail();
        $salon = Salon::where('id', $joueur->salon_id)->firstOrFail();
        $salon->nextPlayer();
    }
}