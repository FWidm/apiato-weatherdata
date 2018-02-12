<?php

namespace App\Containers\WeatherData\Data\Repositories;


use App\Ship\Parents\Repositories\Repository;

/**
 * Class WeatherDataRepository
 */
class WeatherDataRepository extends Repository
{
    protected $container = 'WeatherData';
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'id'    => '=',
    ];

    public function boot()
    {
		parent::boot();
        // probably do some stuff here ...
    }
}
