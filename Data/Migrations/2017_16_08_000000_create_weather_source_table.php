<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

/**
 * Class CreateWeatherDataTable
 * @author Fabian Widmann <fabian.widmann@gmail.com>
 * "station-02074": {
 * "id": "02074",
 * "from": "2004-06-01T09:33:27+00:00",
 * "until": "2017-09-11T09:33:27+00:00",
 * "height": "522",
 * "latitude": "48.3751",
 * "longitude": "8.9801",
 * "name": "Hechingen",
 * "state": "Baden-W\u00fcrttemberg",
 * "active": true
},
 */
class CreateWeatherSourceTable extends Migration
{

    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('weather_source', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->integer('original_id',false,true);
            $table->string('source');
            $table->json('data');


        });

        /**
         * RELATIONS
         */
//        Schema::table('weather_source', function (Blueprint $table) {
//
//        });

    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::drop('weather_source');
    }
}