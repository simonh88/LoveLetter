<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use App\Joueur;
use App\Http\Requests;


class SalonsController extends Controller{
    public function show($n){
        $joueurs = Joueur::where('salon_id', $n)->first();


        return view('salons',['idSalon'=>$n, 'joueur'=>$joueurs->username]);
    }
}
