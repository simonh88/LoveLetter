<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePiochesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('piles_cartes', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('salon_id')->unsigned();
        });

        Schema::table('piles_cartes', function(Blueprint $table){
            $table->foreign('salon_id')->references('id')->on('salons');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //A voir si utile
        /*Schema::table('piles_cartes', function(Blueprint $table) {
            $table->dropForeign('piles_cartes_salon_id_foreign');
        });*/

        Schema::drop('piles_cartes');
    }
}
