<?php

namespace App\Containers\WeatherData\UI\API\Transformers;

use App\Containers\WeatherData\Models\WeatherSource;
use App\Ship\Parents\Transformers\Transformer;

class WeatherSourceTransformer extends Transformer
{
    /**
     * @var  array
     */
    protected $defaultIncludes = [
    ];

    /**
     * @var  array
     */
    protected $availableIncludes = [
    ];

    /**
     * @param WeatherSource $entity
     * @return array
     */
    public function transform(WeatherSource $entity)
    {
        if (!$entity)
            return [];
        $response = [

            'object' => 'WeatherSource',
            'id' => $entity->getHashedKey(),
            'created_at' => $entity->created_at,
            'updated_at' => $entity->updated_at,
            'original_id' => $entity->original_id,
            'source' => $entity->source,
            'data' => $entity->data,


        ];

        $response = $this->ifAdmin([
            'real_id'    => $entity->id,
        ], $response);

        return $response;
    }
}
