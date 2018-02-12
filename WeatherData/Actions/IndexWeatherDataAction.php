<?php

namespace App\Containers\WeatherData\Actions;

use Apiato\Core\Foundation\Facades\Apiato;
use App\Containers\WeatherData\Tasks\ConvertWeatherDataTask;
use App\Containers\WeatherData\UI\API\Requests\IndexWeatherDataRequest;
use App\Ship\Parents\Actions\Action;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Psy\Exception\ErrorException;

class IndexWeatherDataAction extends Action
{

    public function run(IndexWeatherDataRequest $request)
    {
        Log::info("all()=" . print_r($request->all(), true));
        $methods = [];

        if ($request->has(config('weather.request_typeLikeThat_string')))
            $methods[] = ['filterByParameterType' => [$request->get(config('weather.request_typeLikeThat_string'))]];

        if ($request->has(config('weather.request_byParticipantId_string')))
            $methods[] = ['filterByParticipantId' => [$request->get(config('weather.request_byParticipantId_string'))]];

        if ($request->has(config('weather.request_srcLikeThat_string')))
            $methods[] = ['filterBySource' => [$request->get(config('weather.request_srcLikeThat_string'))]];

        if ($request->has(config('weather.request_from_string')) || $request->has(config('weather.request_to_string'))) {
            $from = $request->get(config('weather.request_from_string'));
            $to = $request->get(config('weather.request_to_string'));
            $methods[] = ['filterByDate' => [$from, $to]];
        }
        //always check if queries are accessible from the current authed user (owner of geolocations).
        $methods[] = ['filterByAuthUser' => [Auth::user()->id]];

//        $data = $this->call(IndexWeatherDataTask::class, [], $methods);
//        $data=Apiato::call()
        $data = Apiato::call('WeatherData@IndexWeatherDataTask', [], $methods);

        if ($request->has(config('weather.request_convert_string'))) {
            try {
                $data = $this->call(ConvertWeatherDataTask::class, [$data, $request->get(config('weather.request_convert_string'))]);
            } catch (ErrorException $exception) {

            }
        }

        return $data;
    }
}
