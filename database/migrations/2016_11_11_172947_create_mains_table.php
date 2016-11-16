<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMainsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mains', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('carte_id')->unsigned();
            $table->integer('joueur_id')->unsigned();
        });

        Schema::table('mains', function(Blueprint $table){
            $table->foreign('carte_id')->references('id')->on('cartes');
            $table->foreign('joueur_id')->references('id')->on('joueurs');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('mains', function(Blueprint $table) {
            $table->dropForeign('mains_carte_id_foreign');
            $table->dropForeign('mains_joueur_id_foreign');
        });
        Schema::drop('mains');
    }
}
