<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Action extends Model
{
    protected $table = 'actions';
    protected $primaryKey = 'id';//Par défaut, pas besoin de le spécifier là

    public $timestamps = false;
    protected $fillable = ['salon_id', 'type', 'source', 'message', 'carte_id'];


    public function salon(){
        return $this->belongsTo('App\Models\Salon');
    }

    /**
     * Un joueur joue une carte
     * @param Joueur $joueur
     * @param Cartes $card
     */
    public static function joueurJoue(Joueur $joueur, $carte_id) {
        Action::create([
            'salon_id' => $joueur->getSalon()->id,
            'type' => 'PLAY',
            'source' => $joueur->username,
            'message' => Cartes::where('id', $carte_id)->first()->nom,
            'carte_id' => $carte_id,
        ]);
    }

    /**
     * Un joueur envoie un message sur son salon
     * @param Joueur $joueur
     * @param $message
     */
    public static function joueurChat(Joueur $joueur, $message) {
        Action::create([
            'salon_id' => $joueur->getSalon()->id,
            'type' => 'CHAT',
            'source' => $joueur->username,
            'message' => $message,
        ]);
    }

    /**
     * Message du serveur sur un salon
     * @param Salon $salon
     * @param $message
     */
    public static function messageServeur(Salon $salon, $message) {
        Action::create([
            'salon_id' => $salon->id,
            'type' => 'CHAT',
            'source' => 'SERVEUR',
            'message' => $message,
        ]);
    }

    public static function messageDebug(Salon $salon, $message) {
        Action::create([
            'salon_id' => $salon->id,
            'type' => 'CHAT',
            'source' => 'DEBUG',
            'message' => $message,
        ]);
    }


}
