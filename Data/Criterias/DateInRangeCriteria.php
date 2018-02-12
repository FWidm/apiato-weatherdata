<?php

namespace App\Containers\WeatherData\Data\Criterias;

use App\Ship\Parents\Criterias\Criteria;
use Carbon\Carbon;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\Log;
use Prettus\Repository\Contracts\RepositoryInterface as PrettusRepositoryInterface;

class DateInRangeCriteria extends Criteria
{

    /**
     * @var Carbon
     */
    private $from;

    /**
     * @var Carbon
     */
    private $to;


    public function __construct(Carbon $from,Carbon $to)
    {
        $this->from = $from;
        $this->to = $to;
    }

    /**
     * Applies the criteria
     * @param Builder $model
     * @param \Prettus\Repository\Contracts\RepositoryInterface $repository
     * @return  mixed
     */
    public function apply($model, PrettusRepositoryInterface $repository)
    {
        return $model->whereBetween('date',[$this->from->toDateString(),$this->to->toDateString()]);
    }

}