<?php

namespace App\Containers\WeatherData\UI\API\Transformers;

use App\Containers\WeatherData\Models\t;
use App\Ship\Parents\Transformers\Transformer;

class DWDStationToSourceTransformer extends Transformer
{
    /**
     * @param $entity
     * @return array
     */
    public function transform($entity)
    {
        return [
            'original_id' => $entity->getId(),
            'data' => $entity,
            'source' => 'DWD',
        ];
    }
}
