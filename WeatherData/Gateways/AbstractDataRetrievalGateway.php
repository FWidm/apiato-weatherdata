<?php
/**
 * User: fabianwidmann
 * Date: 28.09.17
 * Time: 10:57
 */

namespace App\Containers\WeatherData\Gateways;


use Carbon\Carbon;

/**
 * Class AbstractDataRetrievalGateway
 * @package App\Containers\WeatherData\Gateways
 * @author Fabian Widmann <fabian.widmann@gmail.com>
 *
 * Extend this class to add another gateway to other data sources. First implement the *retrieve* function to fetch data,
 * afterwards implement the parseTo* methods to create objects in the expected format.
 *
 */
abstract class AbstractDataRetrievalGateway
{
    /** Retrieve data from one source for a specific point and date
     * @param $lat - latitude
     * @param $lon - longitude
     * @param Carbon $date - date that will be queried
     * @return array - [rawWeatherData,rawSourceData]
     */
    protected abstract function retrieve($lat, $lon, Carbon $date);

    /** Parse from the original format into the expected WeatherData Model  - see
     * @param $object - source weather data in another format
     * @param $geoLocationId - attach this weatherData object to this geoLocation
     * @return array - with attributes of a WeatherData Object. Expected data structure is: obj -> [data -> [attributes: named array]]
     */
    protected abstract function parseToWeatherData($object, $geoLocationId);

    /** Construct an array of attributes for a WeatherSource object
     * @param $object - additional information about the source of the data if available
     * @return array - with attributes of a WeatherSource Object OR empty array if not applicable. Expected data structure is: obj -> [data -> [attributes: named array]]
     */
    protected function parseToWeatherSource($object)
    {
        return [];
    }

    /**
     * Delay of the source in days
     * @return integer delay
     */
    public abstract function getTimeDelayInDays();

    /**
     * Retrieve data from the given data source
     * @param $lat - latitude  (e.g. 49.9384)
     * @param $lon - longitude (e.g. 9.99384)
     * @param Carbon $date - date that will be queried
     * @param $geoLocationId -id of the geoLocation for which we retrieved the data
     * @return array - first element contains the weatherdata array, second contains the sourceData
     */
    public final function getData($lat, $lon, Carbon $date, $geoLocationId)
    {
        [$rawWeatherDataArray, $sourceArray] = $this->retrieve($lat, $lon, $date);
        $weatherDataArray = [];
        $sourceDataArray = [];

        foreach ($rawWeatherDataArray as $paramKey => $paramArray) {
            foreach ($paramArray as $param)
                $weatherDataArray[] = $this->parseToWeatherData($param, $geoLocationId);
        }

        if (isset($sourceArray)) {
            foreach ($sourceArray as $sourceData) {
                $sourceDataArray[] = $this->parseToWeatherSource($sourceData);
            }
        }

        return [$weatherDataArray, $sourceDataArray];
    }


}