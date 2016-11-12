<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Cartes extends Model
{
    protected $table = 'cartes';
    protected $primaryKey = 'id';//Par défaut, pas besoin de le spécifier là

    public $timestamps = false;
}
