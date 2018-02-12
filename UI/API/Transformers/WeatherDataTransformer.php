<?php

namespace App\Containers\WeatherData\UI\API\Transformers;

use App\Containers\GeoLocation\UI\API\Transformers\GeoLocationToParticipantTransformer;
use App\Containers\WeatherData\Models\WeatherData;
use App\Ship\Parents\Transformers\Transformer;

/**
 * Class WeatherDataTransformer
 * @package App\Containers\WeatherData\UI\API\Transformers
 * @author Fabian Widmann <fabian.widmann@gmail.com>
 */
class WeatherDataTransformer extends Transformer
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
        'participant'
    ];

    /**
     * @param WeatherData $entity
     * @return array
     */
    public function transform(WeatherData $entity)
    {
        //get hashed key of the weathersource instead of leaking the real id to the user.
        $weatherSource = $entity->weatherSource ? $entity->weatherSource->getHashedKey() : $this->null();

        $response = [

            'object' => 'WeatherData',
            'id' => $entity->getHashedKey(),
            'created_at' => $entity->created_at,
            'updated_at' => $entity->updated_at,
            'source_id' => $weatherSource,
            'source' => $entity->source,
            'value' => $entity->value,
            'description' => $entity->description,
            'classification' => $entity->classification,
            'distance' => $entity->distance,
            'lat' => $entity->lat,
            'lon' => $entity->lon,
            'date' => $entity->date,
            'type' => $entity->type,
            'geo_location_id' => $entity->encode($entity->geo_location_id),
            'unit' => $entity->unit,

        ];

        $response = $this->ifAdmin([
            'real_id' => $entity->id,
        ], $response);

        return $response;
    }

    /**
     * Includes a weathersource if possible, if not returns a null resource.
     * @param WeatherData $obj
     * @return \League\Fractal\Resource\Item|\League\Fractal\Resource\NullResource
     */
    public function includeSource(WeatherData $obj)
    {
        return $obj->weatherSource ? $this->item($obj->weatherSource, new WeatherSourceTransformer()) : $this->null();
    }

    /**
     * Includes a fake participant model that is generated from the source geoLocation.
     * @param WeatherData $obj
     * @return \League\Fractal\Resource\Item
     */
    public function includeParticipant(WeatherData $obj)
    {
        return $this->item($obj->geoLocation, new GeoLocationToParticipantTransformer(), 'participant');
    }

}
