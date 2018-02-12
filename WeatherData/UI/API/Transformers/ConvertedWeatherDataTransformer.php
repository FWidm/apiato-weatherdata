<?php

namespace App\Containers\WeatherData\UI\API\Transformers;

use App\Containers\WeatherData\Models\WeatherData;
use App\Ship\Parents\Transformers\Transformer;
use Illuminate\Support\Facades\Log;
use Olifolkerd\Convertor\Convertor;

class ConvertedWeatherDataTransformer extends Transformer
{
    /**
     * @var  array
     */
    protected $defaultIncludes = [
        'source'
    ];

    /**
     * @var  array
     */
    protected $availableIncludes = [
    ];

    /**
     * @param WeatherData $entity
     * @return array
     */
    public function transform(WeatherData $entity)
    {
        [$val, $unit] = $this->convertValue($entity->value, $entity->unit);
        $response = [

            'object' => 'WeatherData',
            'id' => $entity->getHashedKey(),
            'created_at' => $entity->created_at,
            'updated_at' => $entity->updated_at,
            'source_id' => $entity->source_id,
            'source' => $entity->source,
            'value' => $val,
            'description' => $entity->description,
            'classification' => $entity->classification,
            'distance' => $entity->distance,
            'lat' => $entity->lat,
            'lon' => $entity->lon,
            'date' => $entity->date,
            'type' => $entity->type,
            'geo_location_id' => $entity->encode($entity->geo_location_id),
            'unit' => $unit,

        ];

        $response = $this->ifAdmin([
            'real_id' => $entity->id,
        ], $response);

        return $response;
    }

    public function includeSource(WeatherData $obj)
    {

        return $obj->weatherSource ? $this->item($obj->weatherSource, new WeatherSourceTransformer()) : null;
    }

    /**
     * @param $val
     * @param $unit
     * @param $targetUnit
     * @throws \Exception if conversion not possible
     */
    private function convertValue($val, $unit, $targetUnit)
    {
        Log::info("Converting: val=" . $val . ", unit" . $unit);
        //todo: conversion
        $conv=new Convertor($val,$unit);

        return $conv->to($targetUnit);
    }
}
