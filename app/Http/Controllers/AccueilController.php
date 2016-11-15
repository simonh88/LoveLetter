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

        $request->session()->set('username', $joueur->username);

        $idSalon = $this->chercherSalon();
        $joueur->salon_id = $idSalon;
        $joueur->is_ready = 1;
        $joueur->save();



        return redirect("salons/".$idSalon);
    }

    /**
     * recherche d'un salon libre, s'il n'y en a pas, on en crée un
     * @return mixed
     */
    protected function chercherSalon(){
        $salon = Salon::where('is_playing', false)->whereColumn('nb_joueurs_presents', '<', 'nb_joueurs_max')->first();

        if(empty($salon)){
            $salon = $this->creationSalon();
        }

        $salon->nb_joueurs_presents += 1;
        $salon->save();

        // TODO Quand le salon est plein, on appelle nextPlayer qui set id_prochain_joueur
        if ($salon->isFull()) {
            $salon->nextPlayer();
        }

        return $salon->id;
    }

    /**
     * Creation d'un salon
     */
    protected function creationSalon(){
        return Salon::create([
           'nb_joueurs_max' => 4,
            'nb_joueurs_presents' => 0,
            'is_playing' => false
        ]);
    }
}
