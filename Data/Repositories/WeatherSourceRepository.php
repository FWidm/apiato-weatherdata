<?php

namespace App\Containers\WeatherData\Data\Repositories;

use App\Ship\Parents\Repositories\Repository;

/**
 * Class WeatherSourceRepository
 */
class WeatherSourceRepository extends Repository
{
    protected $container = 'WeatherData';
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'id' => '=',
        'name' => 'like',
    ];

    public function boot()
    {
        parent::boot();
        // probably do some stuff here ...
    }
}
