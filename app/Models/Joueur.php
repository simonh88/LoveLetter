<?php

namespace App;


use Illuminate\Database\Eloquent\Model;

class Joueur extends Model
{
    protected $table = 'joueurs';
    protected $primaryKey = 'id';//Par défaut, pas besoin de le spécifier là

    protected $fillable = ['username', 'salon_id'];

    public $timestamps = false;

    public function main(){
        return $this->hasMany('App\Models\Main');
    }

    public function salon(){
        return $this->belongsTo('App\Models\Salon');
    }

    public function checkTurn() {
        $salon = Salon::where('id', $this->salon_id)->firstOrFail();
        return $salon->id_prochain_joueur == $this->id;
    }

    public function endTurn() {
        $salon = Salon::where('id', $this->salon_id)->firstOrFail();
        $salon->nextPlayer();
    }

    public function setSalon($idSalon) {
        $this->salon_id = $idSalon;
        $this->is_ready = 1;
        $this->save();
    }

    public function getSalon() {
        return Salon::where('id', $this->salon_id);
    }
}
