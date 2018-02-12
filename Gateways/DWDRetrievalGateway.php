<?php
/**
 * User: fabianwidmann
 * Date: 28.09.17
 * Time: 14:14
 */

namespace App\Containers\WeatherData\Gateways;


use App\Containers\WeatherData\Exceptions\DataNotAvailableException;
use App\Containers\WeatherData\Exceptions\RetrievalFailedException;
use App\Containers\WeatherData\UI\API\Transformers\DWDStationToSourceTransformer;
use App\Containers\WeatherData\UI\API\Transformers\DWDToWeatherDataTransformer;
use Carbon\Carbon;
use FWidm\DWDHourlyCrawler\DWDLib;
use FWidm\DWDHourlyCrawler\Exceptions\DWDLibException;
use FWidm\DWDHourlyCrawler\Hourly\Variables\DWDHourlyParameters;
use FWidm\DWDHourlyCrawler\Model\DWDCompactParameter;
use FWidm\DWDHourlyCrawler\Model\DWDStation;
use Illuminate\Support\Facades\Log;
use InvalidArgumentException;
use Spatie\Fractalistic\Fractal;

class DWDRetrievalGateway extends AbstractDataRetrievalGateway
{

    private $variables;

    public function __construct($variables = null)
    {
        $this->variables = $variables;
    }


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
        Log::info("DWDRetrieveTrait: storing at " . (config('weather.dwd_storage_path')));
        $dwdLib = new DWDLib(config('weather.dwd_storage_path'));

        //add all variables by default.
        if (!$this->variables) {
            $this->variables = new DWDHourlyParameters();
            $this->variables->addAirTemperature()->addCloudiness()->addPrecipitation()
                ->addPressure()->addSoilTemperature()->addSun()->addWind();
        }
        $weatherDataList = [];
        $stationList = [];

        try {
            [$data, $stations] = $dwdLib->getHourlyInInterval($this->variables, $date, $lat, $lon);
            Log::info("DWDRetrieveTrait: Done receiving... " . count($data) . "DWD variables");

            //files are stored in public/output/hourly -> stations & .../hourly/$var -> zip files
            //filter all empty values, if not empty save each dwd compact param to the db
            //todo: try to reduce the nested loops.
            if ($data) {
                foreach ($data as $key => $obj) {
                    foreach ($obj as $value) {
                        /* @var $value \FWidm\DWDHourlyCrawler\Model\DWDAbstractParameter */
                        $weatherDataList[] = $value->exportSingleVariables();
                    }

                }
                if (count($weatherDataList) == 0)
                    throw new RetrievalFailedException("No data found for this date. 
                    Check if the date is older than the threshold for this library.", "RetrieveDWDTrait");

                $stationList = $stations;
            }
        } catch (DWDLibException $e) {
            //recoverable exception todo: check if >= is the correct way to do this.
            if (strpos($e->getMessage(), 'No Stations near the given Coordinates are available ') >= 0) {
                throw new DataNotAvailableException($e->getMessage(), "RetrieveDWDTrait");
            } else
                throw new RetrievalFailedException($e->getMessage(), "RetrieveDWDTrait");
        } catch (InvalidArgumentException $e1) {
            throw new RetrievalFailedException($e1->getMessage(), "RetrieveDWDTrait");
        }
        return [$weatherDataList, $stationList];
    }

    /**
     * Creates an array with values to insert into the database.
     * @param DWDCompactParameter $object
     * @return array for the model insertion
     */
    protected function parseToWeatherData($object, $geoLocationId)
    {
        $object->geoLocationId = $geoLocationId;
        $array = Fractal::create($object, DWDToWeatherDataTransformer::class)->toArray();
        return $array;
    }

    /**
     * Creates an array that is compatible with the `weather_source_metadata` table.
     * @param DWDStation $object
     * @return array
     */
    protected function parseToWeatherSource($object)
    {
        $array = Fractal::create($object, DWDStationToSourceTransformer::class)->toArray();
        return $array;
    }

    public function getTimeDelayInDays()
    {
        return 1;
    }
}