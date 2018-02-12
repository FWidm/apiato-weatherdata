<?php

namespace App\Containers\WeatherData\Tasks;

use App\Containers\WeatherData\Exceptions\ConversionStringMalformedException;
use App\Containers\WeatherData\Models\WeatherData;
use App\Ship\Parents\Tasks\Task;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Log;
use Olifolkerd\Convertor\Convertor;
use Olifolkerd\Convertor\Exceptions\ConvertorDifferentTypeException;
use Olifolkerd\Convertor\Exceptions\ConvertorException;
use Olifolkerd\Convertor\Exceptions\ConvertorInvalidUnitException;

class ConvertWeatherDataTask extends Task
{

    public function __construct()
    {

    }

    public function run($weatherDataArray, $conversionsString)
    {

        Log::info("conversion=" . $conversionsString);
        Log::info("wd type=" . get_class($weatherDataArray));

        $targetUnitArray = $this->parseConversionString($conversionsString);
        //Array of weatherData Ids to make sure that each value is converted only once.
        $converted = [];
        foreach ($targetUnitArray as $unit => $values) {
            if (in_array(config('weather.request_wildcard'), $values))
                $this->convert($weatherDataArray, $converted, $unit);
            else
                $this->convert($weatherDataArray, $converted, $unit, $values);

            Log::info("Added ids to converted are: " . join(',', $converted));
        }

        return $weatherDataArray;
    }

    /**
     * Converts data entries from one unit to another one.
     * @param $weatherData - data set
     * @param $converted - list of already converted data set entries (id only)
     * @param $targetUnit - target unit
     * @param array $values - specific values we want to
     */
    private function convert(&$weatherData, &$converted, $targetUnit, $values = [])
    {
        //Convert single value
        if (get_class($weatherData) == WeatherData::class) {
            //todo: trycatch?
            $this->convertWeatherData($weatherData, $targetUnit);
            return;
        }
        if (is_iterable($weatherData)) {
            Log::info("Converting values=" . implode(',', $values) . " to unit=$targetUnit");
            foreach ($weatherData as $item) {
                try {
                    //figure out if we can skip the conversion
                    if ((count($values) > 0 && !in_array($item->type, $values)) //searching for specific values: check if values are set, then check if its not in the searched values
                        || in_array($item->id, $converted) || (strtoupper($item->unit) == strtoupper($targetUnit))) // already converted OR unit is already the same.
                        continue;
                    Log::info("Converting id=" . $item->id . " to $targetUnit");
                    $this->convertWeatherData($item, $targetUnit);
                } catch (ConvertorInvalidUnitException | ConvertorDifferentTypeException | ConvertorException $exception) {
                    Log::info("Convertor exception=" . $exception->getMessage());
                    continue;
                }
                $converted[] = $item->id;
            }
            return;
        }
        //If the weatherData content is neither of type WeatherData::class nor an iterable, this conversion fails.
        throw new \InvalidArgumentException("WeatherData is not an iterable object or of type WeatherData. Class of the given object: ".get_class($weatherData));
    }

    /**Convert the given item to the target unit
     * @param $item - reference to the item
     * @param $targetUnit
     */
    public function convertWeatherData(&$item, $targetUnit)
    {
        $conv = new Convertor($item->value, strtolower($item->unit));
        $item->value = $conv->to(strtolower($targetUnit));
        $item->unit = strtoupper($targetUnit);
    }

    /**
     * Parses the given conversion into a named array that has the expected units as key and the variables to convert as value
         * e.g. ?convert=a,b,c:C results in
     *    [C] => Array
     * (
     * [0] => a
     * [1] => b
     * [2] => c
     * )
     * @param $string
     * @return array
     */
    private function parseConversionString($string)
    {
        //?convert=a,b,c:C;d,e,f:km%20s**-1
        $conversion_entry = explode(config('weather.request_item_separator'), $string);
        Log::info("Conversions=" . print_r($conversion_entry, true));
        $conversions = [];

        foreach ($conversion_entry as $conversion) {
            $split = explode(config('weather.request_conversion_unit_separator'), $conversion);
            if (count($split) != 2)
                throw new ConversionStringMalformedException("Missing colon to indicate the end of the variable part and beginning of the unit. Error in the string: '" . $conversion . "'.'");
            //remove all rempty values after splitting the array => a,b,:C is valid but would make the array ['a','B','']
            $variables = array_filter(explode(config('weather.request_item_value_separator'), $split[0]));
            $unit = $split[1];
            $conversions[$unit] = $variables;
        }
        Log::info("Conversions=" . print_r($conversions, true));
        return $conversions;
    }
}