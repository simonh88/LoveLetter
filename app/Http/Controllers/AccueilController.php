<?php



namespace App\Http\Controllers;

use App\Cartes;
use Illuminate\Http\Request;

use App\Http\Requests;
use Illuminate\Support\Facades\Session;
use App\Joueur;
use App\Salon;
use App\PileCartes;
use App\CartesDansPile;


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
            var_dump($salon);
            $salon = $this->creationSalon();
        }

        $salon->nb_joueurs_presents += 1;
        $salon->save();

        // TODO Quand le salon est plein, on set id_prochain joueur

        return $salon->id;
    }

    /**
     * Creation d'un salon
     */
    protected function creationSalon(){
        $salon =  Salon::create([
           'nb_joueurs_max' => 4,
            'nb_joueurs_presents' => 0,
            'is_playing' => false
        ]);
        $this->peuplementCartesDansPioche($this->creationPilesDeSalon($salon->id));
        return $salon;
    }

    /**
     * On crée deux piles une pour les cartes défaussées
     * et une autre pour la pioche
     * @param $idSalon
     * @return mixed
     */
    protected function creationPilesDeSalon($idSalon){
        //Creation de la pile pour defausser les cartes
        $defausse = PileCartes::create([
            'salon_id' => $idSalon,
            'estPioche' => false
        ]);


        $pioche = PileCartes::create([
            'salon_id' =>$idSalon,
            'estPioche' => true
        ]);
        $pioche->save();

        return $pioche->id;
    }

    /**
     * on peuple la pioche avec toutes les cartes disponibles
     * @param $idPioche
     */
    protected function peuplementCartesDansPioche($idPioche){
        $cartes = Cartes::all();
        foreach ($cartes as $carte){
            echo($carte->id);
            $c = CartesDansPile::firstOrNew([
                'carte_id' => $carte->id,
                'pile_cartes_id' => $idPioche
            ]);
            $c->save();
        }
    }

}
