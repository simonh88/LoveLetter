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

        $user_id = $req->session()->get('user_id');
        $turn = self::checkTurn($user_id);
        $res = array('myturn' => $turn);

        // TODO si $turn est vrai, le joueur pioche une carte


        return json_encode($res);
    }

    public static function checkTurn($user_id) {
        $joueur = Joueur::where('id', $user_id)->firstOrFail();
        $salon = Salon::where('id', $joueur->salon_id);
        return $joueur->id == $salon->id_prochain_joueur;
    }
}