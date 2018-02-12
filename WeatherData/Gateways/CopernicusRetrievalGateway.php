<?php
/**
 * Created by PhpStorm.
 * User: fabianwidmann
 * Date: 28.09.17
 * Time: 10:59
 */

namespace App\Containers\WeatherData\Gateways;


use App\Containers\WeatherData\Exceptions\RetrievalFailedException;
use App\Containers\WeatherData\UI\API\Transformers\CopernicusToWeatherDataTransformer;
use Carbon\Carbon;
use FWidm\DWDHourlyCrawler\Hourly\DWDHourlyCrawler;
use FWidm\DWDHourlyCrawler\Model\DWDCompactParameter;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Support\Facades\Log;
use Spatie\Fractalistic\Fractal;

class CopernicusRetrievalGateway extends AbstractDataRetrievalGateway
{
    /**
     * Retrieves data from the DWDLib. Returns all single parameters in an array.
     * @param $lat - latitude of the call
     * @param $lon - longitude of the call
     * @param Carbon $date - date of the call
     * @param null $variables - needed DWDHourlyParameters - adds all by default.
     * @return  array of DWDCompactParameter
     */
    protected function retrieve($lat, $lon, Carbon $date)
    {
        try {
            $dataArray = $this->getCopernicusData($lat, $lon, $date);
        } catch (RetrievalFailedException $e) {
            Log::info("Retrieval failed... fetching data for this day.");
            $this->fetchCopernicusData($date);
            $dataArray = $this->getCopernicusData($lat, $lon, $date);
        }
        return [$dataArray, []];
    }

    /**
     * Creates an array with values to insert into the database. todo: maybe replace with transformers
     * @param DWDCompactParameter $dwdParam
     * @return array for the model insertion
     */
    protected function parseToWeatherData($object, $geoLocationId)
    {
        $object->geoLocationId = $geoLocationId;
        $array = Fractal::create($object, CopernicusToWeatherDataTransformer::class)->toArray();
        return $array;
    }

    /**
     * @param Carbon $date Fetch Copernicus params from the given host+port
     */
    private function fetchCopernicusData(Carbon $date)
    {
        $client = new Client();
        $url = config('weather.copernicus_host');
        Log::info(config('GET', $url . '/retrieve'));

        $res = $client->request('GET', $url . '/retrieve', [
            'query' => ['timestamp' => $date->toIso8601String()]
        ]);
    }

    /**
     * Retrieves data from the DWDLib. Returns all single parameters in an array.
     * @param $lat - latitude of the call
     * @param $lon - longitude of the call
     * @param Carbon $date - date of the call
     * @param null $variables - needed DWDHourlyParameters - adds all by default.
     * @return  array of DWDCompactParameter
     */
    private function getCopernicusData($lat, $lon, Carbon $date): array
    {
        $client = new Client();
        $url = config('weather.copernicus_host');
        $options = [
            'query' => [
                'timestamp' => $date->toIso8601String(),
                'lat' => $lat,
                'lon' => $lon,
            ]
        ];
        try {
            $res = $client->request('GET', $url . '/parse', $options);
            $statuscode = $res->getStatusCode();
            $body = \GuzzleHttp\json_decode($res->getBody());

            return (array)$body;

        } catch (\Exception $exception) {
            throw new RetrievalFailedException($exception->getMessage(), 'Copernicus');
        }

    }

    public function getTimeDelayInDays()
    {
        return 5;
    }
}