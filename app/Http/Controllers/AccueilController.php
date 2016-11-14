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
        $joueur = Joueur::firstOrNew(['username' => $request->input('username')]);
        $idSalon = $this->chercherSalon();
        $joueur->salon_id = $idSalon;
        $joueur->is_ready = 1;
        $joueur->save();



        return redirect("salons/".$idSalon);
    }

    protected function chercherSalon(){
        $salon = Salon::where('nb_joueurs_max', '>', 'nb_joueurs_presents')->first();

        $salon->nb_joueurs_presents += 1;
        $salon->save();
        return $salon->id;
    }
}
