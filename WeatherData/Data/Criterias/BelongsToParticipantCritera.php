<?php

namespace App\Containers\WeatherData\Data\Criterias;

use App\Containers\GeoLocation\Data\Repositories\GeoLocationRepository;
use App\Ship\Parents\Criterias\Criteria;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use Prettus\Repository\Contracts\RepositoryInterface as PrettusRepositoryInterface;

/**
 * Class BelongsToParticipantCritera
 * @package App\Containers\WeatherData\Data\Criterias
 * @author Fabian Widmann <fabian.widmann@gmail.com>
 */
class BelongsToParticipantCritera extends Criteria
{

    /**
     * @var string
     */
    private $participantIds;

    public function __construct($participantIds)
    {
        $this->participantIds=$participantIds;
    }

    /**
     * Apply the critera to the model.
     * @param Builder $model
     * @param PrettusRepositoryInterface $repository
     * @return mixed
     */
    public function apply($model, PrettusRepositoryInterface $repository)
    {
        $ids=explode(config('weather.request_item_separator'),$this->participantIds);
        //fetch ids of
        $geoLocationIds = App::make(GeoLocationRepository::class)->findWhereIn('participant_id', $ids, ['id']);
        Log::info("geolocIds=".$geoLocationIds);
        $retQuery = $model->whereIn('geo_location_id',$geoLocationIds);
//        Log::info("retQuery=".$retQuery->toSql());
        return $retQuery;
    }

}
