<?php

namespace App\Containers\WeatherData\Actions;

use App\Containers\WeatherData\Exceptions\WeatherDataNotFound;
use App\Containers\WeatherData\Tasks\ConvertWeatherDataTask;
use App\Containers\WeatherData\Tasks\FindWeatherDataTask;
use App\Containers\WeatherData\UI\API\Requests\FindWeatherDataRequest;
use App\Ship\Parents\Actions\Action;
use Illuminate\Support\Facades\Auth;

class FindWeatherDataAction extends Action
{
    public function run(FindWeatherDataRequest $request)
    {
        $id = $request->id;
        $var = $this->call(FindWeatherDataTask::class, [$id], [['filterByAuthUser' => [Auth::user()->id]]]);
        if ($request->has(config('weather.request_convert_string'))) {
                $data = $this->call(ConvertWeatherDataTask::class, [$var, $request->get(config('weather.request_convert_string'))]);
        }

        if(!$var)
            throw new WeatherDataNotFound();
        return $var;
    }
}
