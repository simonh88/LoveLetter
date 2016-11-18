<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class CreateActionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('actions', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('salon_id')->unsigned();
            $table->string('type');
            $table->string('source');
            $table->string('message');
            $table->integer('carte_id')->unsigned();
        });

        Schema::table('actions', function (Blueprint $table) {
            $table->foreign('salon_id')->references('id')->on('salons');
            $table->foreign('carte_id')->references('id')->on('cartes');
        }) ;
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('actions', function (Blueprint $table) {
            $table->dropForeign('actions_salon_id_foreign');
            $table->dropForeign('actions_carte_id_foreign');
        }) ;
        Schema::drop('actions');
    }
}
