<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCartesPileTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cartes_dans_pile', function (Blueprint $table) {
            $table->increments('id');//A voir si bien ou pas
            $table->integer('carte_id')->unsigned();
            $table->integer('pile_cartes_id')->unsigned();
        });

        Schema::table('cartes_dans_pile', function(Blueprint $table){
            $table->foreign('carte_id')->references('id')->on('cartes');
            $table->foreign('pile_cartes_id')->references('id')->on('piles_cartes');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {

        Schema::table('cartes_dans_pile', function(Blueprint $table) {
            $table->dropForeign('cartes_dans_pile_carte_id_foreign');
            $table->dropForeign('cartes_dans_pile_salon_id_foreign');
        });
        Schema::drop('cartes_dans_pile');
    }
}
