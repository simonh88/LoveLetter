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
        $res = array('myturn' => $turn);

        // TODO si $turn est vrai, le joueur pioche une carte
        if ($turn) {
            $res['card'] = 4;
            // $res['card'] = $joueur->piocherCarte();
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