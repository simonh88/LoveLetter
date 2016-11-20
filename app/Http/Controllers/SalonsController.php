<?php

namespace App\Http\Controllers;


use App\Salon;
use App\Action;
use Illuminate\Http\Request;
use App\Joueur;
use App\Http\Requests;


class SalonsController extends Controller{

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function show(Request $req, $n){
        // TODO le joueur rejoint le salon
        $joueur = Joueur::getJoueurByUsername($req->session()->get('username'));

        if ($joueur->dansAucunSalon() ) {
            $joueur->setSalon($n);
            $salon = $joueur->getSalon();
            Action::messageServeur($salon, "Bienvenue à " . $joueur->username);

        }
        return view('salons',['idSalon'=>$n]);
    }

    public function showAll() {
        $salons = Salon::all()->toArray();
        //return view('allSalons', ['salons' => $salons]);
        return view('allSalons')->with('salons', $salons);
    }
}
