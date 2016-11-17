<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Action extends Model
{
    protected $table = 'actions';
    protected $primaryKey = 'id';//Par défaut, pas besoin de le spécifier là

    public $timestamps = false;
    protected $fillable = ['salon_id', 'type', 'source', 'message'];


    public function salon(){
        return $this->belongsTo('App\Models\Salon');
    }
}
