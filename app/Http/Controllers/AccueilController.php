<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use Illuminate\Support\Facades\Session;

class AccueilController extends Controller
{
    public function getInfos(){
        return view('accueil');
    }

    public function postInfos(Request $request)
    {
        $joueur = new Joueur;
        $joueur->username = $request->input('username');
        $id = $joueur->id;
        $joueur->save();
        $_SESSION = new Session();
        $_SESSION["username"] = $id;
        var_dump($id);
    }
}
