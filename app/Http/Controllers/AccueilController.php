<?php



namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use Illuminate\Support\Facades\Session;
use App\Joueur;
use App\Salon;


class AccueilController extends Controller
{
    public function getInfos(){
        return view('accueil');
    }

    public function postInfos(Request $request){

        $joueur = Joueur::creerJoueur($request->input('username'));
        $request->session()->set('username', $joueur->username);

        $salon = Salon::chercherSalon();
        $joueur->setSalon($salon->id);
        if ($salon->isFull()) {
            $salon->commencer();
        }

        return redirect("salons/".$salon->id);
    }



}
