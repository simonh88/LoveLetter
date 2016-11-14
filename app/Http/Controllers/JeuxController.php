<?php
/**
 * Created by PhpStorm.
 * User: guillaumepetit
 * Date: 14/11/2016
 * Time: 22:15
 */

namespace App\Http\Controllers;


class JeuxController extends Controller
{
    public function myturn() {
        $res = array('myturn' => false);
        return json_encode($res);
    }
}