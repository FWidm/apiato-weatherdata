<?php

namespace App\Ship\Criterias\Eloquent;

use App\Ship\Parents\Criterias\Criteria;
use Carbon\Carbon;
use Illuminate\Database\Query\Builder;
use Prettus\Repository\Contracts\RepositoryInterface as PrettusRepositoryInterface;

/**
 * Class ColumnBetweenDatesCriteria
 * @package App\Containers\WeatherData\Data\Criterias
 * @author Fabian Widmann <fabian.widmann@gmail.com>
 *
 * Retrieves all entities whose date $column's value is between $from and $to.
 */
class ColumnBetweenDatesCriteria extends Criteria
{

    /**
     * @var Carbon
     */
    private $from;

    /**
     * @var Carbon
     */
    private $to;

    private $columnName;


    public function __construct($columnName, Carbon $from, Carbon $to)
    {
        $this->from = $from;
        $this->to = $to;
        $this->columnName = $columnName;
    }

    /**
     * Applies the criteria
     * @param Builder $model
     * @param \Prettus\Repository\Contracts\RepositoryInterface $repository
     * @return  mixed
     */
    public function apply($model, PrettusRepositoryInterface $repository)
    {
        return $model->whereBetween($this->columnName, [$this->from->toDateString(), $this->to->toDateString()]);
    }

}