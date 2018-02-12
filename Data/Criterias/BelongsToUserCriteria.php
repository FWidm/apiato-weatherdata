<?php

namespace App\Containers\WeatherData\Data\Criterias;

use App\Containers\GeoLocation\Data\Repositories\GeoLocationRepository;
use App\Ship\Parents\Criterias\Criteria;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use Prettus\Repository\Contracts\RepositoryInterface as PrettusRepositoryInterface;

/**
 * Class BelongsToUserCriteria
 * @package App\Containers\WeatherData\Data\Criterias
 * @author Fabian Widmann <fabian.widmann@gmail.com>
 */
class BelongsToUserCriteria extends Criteria
{

    /**
     * @var int
     */
    private $userId;


    /**
     * OwnerOfGeoLocationCriteria constructor.
     * @param $userId
     */
    public function __construct($userId)
    {
        $this->userId = $userId;
    }

    /**
     * @param Builder $model
     * @param PrettusRepositoryInterface $repository
     * @return mixed
     */
    public function apply($model, PrettusRepositoryInterface $repository)
    {
        $geoLocations = App::make(GeoLocationRepository::class)->findByField('user_id', $this->userId, ['id']);
        Log::info("geolocs=".$geoLocations);
        $retQuery = $model->whereIn('geo_location_id',$geoLocations);
        Log::info("retQuery=".$retQuery->toSql());

//        $model = $model->where('user_id', '=', $this->userId);
//        Log::info($model->toSql());
        return $model;

    }

}
