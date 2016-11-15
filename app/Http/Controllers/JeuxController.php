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
        $turn = self::checkTurn($username);
        $res = array('myturn' => $turn);

        // TODO si $turn est vrai, le joueur pioche une carte


        return json_encode($res);
    }

    public static function checkTurn($username) {
        $joueur = Joueur::where('username', $username)->firstOrFail();
        $salon = Salon::where('id', $joueur->salon_id)->firstOrFail();
        return $joueur->id == $salon->id_prochain_joueur;
    }
}