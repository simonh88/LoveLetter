<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSalonsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('salons', function(Blueprint $table) {
            $table->increments('id');
            $table->integer('nb_joueurs_max')->unsigned();
            $table->integer('id_prochain_joueur')->unsigned();
            $table->boolean('is_playing');
            $table->integer('nb_joueurs_presents')->unsigned();
            $table->integer('no_manche')->unsigned();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('salons');
    }
}
