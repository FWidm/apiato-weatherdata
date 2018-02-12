<?php

namespace App\Containers\WeatherData\UI\API\Controllers;

use App\Containers\WeatherData\Actions\FindWeatherDataAction;
use App\Containers\WeatherData\Actions\IndexWeatherDataAction;
use App\Containers\WeatherData\UI\API\Requests\FindWeatherDataRequest;
use App\Containers\WeatherData\UI\API\Requests\IndexWeatherDataRequest;
use App\Containers\WeatherData\UI\API\Transformers\WeatherDataTransformer;
use App\Ship\Parents\Controllers\ApiController;

class Controller extends ApiController
{
    /**
     * Show all entities
     */
    public function index(IndexWeatherDataRequest $request)
    {
        $items = $this->call(IndexWeatherDataAction::class, [$request]);
        //todo: ?geoLocationId=x -> get a list of all weatherdata for this id

        return $this->transform($items, WeatherDataTransformer::class/*null, [] Set meta custom data*/);

    }

    /**
     * Show one entity
     */
    public function show(FindWeatherDataRequest $request)
    {
        $message = $this->call(FindWeatherDataAction::class, [$request]);
        //todo: enable conversion + filtering specific params

        return $this->transform($message, WeatherDataTransformer::class);
    }
}
