<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PileCartes extends Model
{
    protected $table = 'piles_cartes';
    protected $primaryKey = 'id';//Par défaut, pas besoin de le spécifier là

    public $timestamps = false;
}
