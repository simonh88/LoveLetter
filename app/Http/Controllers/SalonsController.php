<?php

namespace App\Http\Controllers;


use App\Salon;
use App\Action;
use Illuminate\Http\Request;
use App\Joueur;
use App\Http\Requests;
use Illuminate\Support\Facades\Session;


class SalonsController extends Controller{

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function show($n){
        $joueur = Joueur::getJoueurConnecte();

        $salon = Salon::getSalonById($n);

        if ($salon->isFull() && $joueur->salon_id != $n) {
            return redirect('/');

        }

        if ($joueur->dansAucunSalon() ) {
            $joueur->setSalon($n);
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
