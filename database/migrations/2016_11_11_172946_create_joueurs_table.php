<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateJoueursTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('joueurs', function(Blueprint $table) {
            $table->increments('id');
            $table->string('username')->unique();
            $table->integer('salon_id')->unsigned();
            $table->boolean('is_ready');
            //$table->boolean('aPioche');
        });

        Schema::table('joueurs', function(Blueprint $table){
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

        Schema::table('joueurs', function(Blueprint $table) {
            $table->dropForeign('joueurs_salon_id_foreign');
        });
        Schema::drop('joueurs');
    }
}
