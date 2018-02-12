<?php

namespace App\Containers\WeatherData\UI\API\Transformers;

use App\Ship\Parents\Transformers\Transformer;
use Carbon\Carbon;

class CopernicusToWeatherDataTransformer extends Transformer
{
    /**
     * @param $entity
     * @return array
     */
    public function transform($entity)
    {
        $description = $entity->description;
        $description->index = $entity->index;
        $unit = $entity->description->units;
        //remove unwanted values.
        if(isset($description->units))
            unset($description->units);
        if(isset($description->convertedUnit))
            unset($description->convertedUnit);

        return [
            'source_id' => null,
            'source' => 'Copernicus',
            'value' => $entity->value,
            'description' => $description,
            'classification' => $entity->classification,
            'distance' => $entity->distance,
            'lat' => $entity->latitude,
            'lon' => $entity->longitude,
            'date' => Carbon::parse($entity->date),
            'type' => $entity->type,
            'geo_location_id' => $entity->geoLocationId,
            'unit' => $unit
        ];
    }
}
