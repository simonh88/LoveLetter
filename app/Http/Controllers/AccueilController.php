<?php



namespace App\Http\Controllers;

use App\Action;
use Illuminate\Http\Request;

use App\Http\Requests;
use Illuminate\Support\Facades\Session;
use App\Joueur;
use App\Salon;


class AccueilController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }


    public function getInfos(){
        return view('accueil');
    }

    public function postInfos(Request $request){

        $joueur = Joueur::creerJoueur($request->input('username'));
        $request->session()->set('username', $joueur->username);

        return redirect("salons");
    }



}
