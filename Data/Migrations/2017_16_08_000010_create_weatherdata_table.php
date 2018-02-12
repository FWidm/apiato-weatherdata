<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;


class CreateWeatherDataTable extends Migration
{

    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('weather_data', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            //polymorphic?

            //data
            $table->integer('source_id')->unsigned()->nullable();//->default(null);
            $table->string("source"); //e.g. dwd/ecmwf...
            $table->decimal('value', 12, 6);
            $table->json('description');
            $table->string('classification');
            //from c
            $table->decimal('distance', 12, 6);
            //latitude of the measured val
            $table->decimal('lat', 10, 6);
            //longitude of the measured val
            $table->decimal('lon', 10, 6);
            $table->dateTime('date');
            $table->string('type');
            $table->integer('geo_location_id')->unsigned();
            $table->string('unit');
        });

        /**
         * RELATIONS
         */
        Schema::table('weather_data', function (Blueprint $table) {
            $table->foreign('geo_location_id')
                ->references('id')->on('geo_location')
                ->onDelete('cascade');
            $table->foreign('source_id')->references('id')->on('weather_source')
                ->onDelete('set null');
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::drop('weather_data');
    }
}