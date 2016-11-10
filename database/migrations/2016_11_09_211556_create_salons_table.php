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
            $table->increments('idSalon');
            $table->integer('nbJoueurs');
            $table->integer('idJoueurProchain');
            $table->integer('idPioche');
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
