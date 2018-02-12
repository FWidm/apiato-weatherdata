<?php

namespace App\Containers\WeatherData\Models;

use App\Containers\GeoLocation\Models\GeoLocation;
use App\Ship\Parents\Models\Model;
use Apiato\Core\Traits\HasResourceKeyTrait;

class WeatherData extends Model
{
    protected $table = 'weather_data';

    /**
     * A resource key to be used by the the JSON API Serializer responses.
     */
    protected $resourceKey = 'environmental-data';

    protected $fillable = [
        'source_id',
        'source',
        'value',
        'description',
        'classification',
        'distance',
        'lat',
        'lon',
        'date',
        'type',
        'geo_location_id',
        'unit',
    ];

    protected $hidden = [];

    protected $casts = [
        'description' => 'array',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'date',
    ];

    public function weatherSource()
    {
        return $this->belongsTo(WeatherSource::class,'source_id');
    }

    public function geoLocation(){
        return $this->belongsTo(GeoLocation::class,'geo_location_id');
    }

}
