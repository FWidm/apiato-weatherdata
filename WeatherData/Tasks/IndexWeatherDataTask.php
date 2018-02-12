<?php

namespace App\Containers\WeatherData\Tasks;

use Apiato\Core\Traits\HashIdTrait;
use App\Containers\WeatherData\Data\Criterias\BelongsToParticipantCritera;
use App\Containers\WeatherData\Data\Criterias\BelongsToUserCriteria;
use App\Containers\WeatherData\Data\Criterias\DateInRangeCriteria;
use App\Containers\WeatherData\Data\Criterias\ThisLikeThatCriteria;
use App\Containers\WeatherData\Data\Repositories\WeatherDataRepository;
use App\Containers\WeatherData\Exceptions\InvalidParameterException;
use App\Ship\Parents\Tasks\Task;
use Carbon\Carbon;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;

class IndexWeatherDataTask extends Task
{

    private $repository;

    public function __construct(WeatherDataRepository $repository)
    {
        $this->repository = $repository;
    }

    public function run()
    {
        //todo: filter access by user id
        return $this->repository->paginate();
    }

    public function filterByAuthUser($id)
    {
        Log::info("auth user=" . $id);
        $this->repository->pushCriteria(new BelongsToUserCriteria($id));
    }

    public function filterByParticipantId($id)
    {
        Log::info("participantId=" . $id);
        $this->repository->pushCriteria(new BelongsToParticipantCritera($id));
    }

    public function filterByParameterType($type)
    {
        Log::info("paramType=" . $type);
        $this->repository->pushCriteria(new ThisLikeThatCriteria('type', $type));
    }

    public function filterBySource($src)
    {
        Log::info("source=" . $src);
        $this->repository->pushCriteria(new ThisLikeThatCriteria('source', $src));
    }

    public function filterByDate($from, $to)
    {
        Log::info("1 from=" . $from . "; to=" . $to);

        if (strlen($from) <= 1 && strlen($to) <= 1)
            throw new InvalidParameterException("Either 'to'=$to or 'from'=$from parameter was invalid. Please provide a valid time representation.");
        //see Application container => Service Provider
        $dateHelper = App::make('App\Containers\Application\Util\DateHelper');

        $from = $dateHelper->parseTimeAsPatterns($from, config('weather.accepted_date_formats'));
        $to = $dateHelper->parseTimeAsPatterns($to, config('weather.accepted_date_formats'));

        //if either value is missing, but the other one exists, set defaults.
        // if from is missing - default to the earliest possible date
        // if no to is specified look for data from $from to "now"
        if (!$from)
            $from = Carbon::createFromTimestamp(-1); //earliest timestamp ~ 1969-12-31 23:59:59
        if (!$to)
            $to = Carbon::now(); //now
        Log::info("2 from=" . $from . "; to=" . $to);

        $this->repository->pushCriteria(new DateInRangeCriteria($from, $to));
    }
}
