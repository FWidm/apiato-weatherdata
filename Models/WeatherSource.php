<?php

namespace App\Containers\WeatherData\Models;

use App\Ship\Parents\Models\Model;
use Apiato\Core\Traits\HasResourceKeyTrait;

class WeatherSource extends Model
{

    protected $table = 'weather_source';

    protected $resourceKey = 'weather-source';


    protected $fillable = [
        'original_id',
        'source',
        'data',
    ];

    protected $hidden = [];

    protected $casts = [
        'data' => 'array',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
    ];
}
