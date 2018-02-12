<?php

namespace App\Containers\WeatherData\UI\API\Transformers;

use App\Containers\WeatherData\Models\t;
use App\Ship\Parents\Transformers\Transformer;
use Carbon\Carbon;
use FWidm\DWDHourlyCrawler\Model\DWDCompactParameter;

class DWDToWeatherDataTransformer extends Transformer
{
    /**
     * @param DWDCompactParameter $entity
     * @return array
     */
    public function transform($entity)
    {
        $description = (object) $entity->getDescription();

        $unit = $description->units;
        //remove unwanted values.
        if(isset($description->units))
            unset($description->units);
        if(isset($description->convertedUnit))
            unset($description->convertedUnit);

        return [
            'source_id' => $entity->getStationID(),
            'source' => 'DWD',
            'value' => $entity->getValue(),
            'description' => $entity->getDescription(),
            'classification' => $entity->getClassification(),
            'distance' => $entity->getDistance(),
            'lat' => $entity->getLatitude(),
            'lon' => $entity->getLongitude(),
            'date' => Carbon::parse($entity->getDate()),
            'type' => $entity->getType(),
            'geo_location_id' => $entity->geoLocationId,
            'unit' => $entity->getDescription()['units'],
        ];
    }
}
