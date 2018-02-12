<?php

namespace App\Containers\WeatherData\Data\Criterias;

use App\Ship\Parents\Criterias\Criteria;
use Carbon\Carbon;
use Prettus\Repository\Contracts\RepositoryInterface as PrettusRepositoryInterface;

/**
 * Class OwnerOrAdminCritera
 * @package App\Containers\GeoLocation\Data\Criterias
 * @author Fabian Widmann <fabian.widmann@gmail.com>
 */
class OlderThanDateCriteria extends Criteria
{

    /**
     * @var int
     */
    private $days;
    /**
     * @var string
     */
    private $column;

    public function __construct(string $column, int $days = 0)
    {
        $this->days = $days;
        $this->column = $column;
    }

    /**
     * Apply the critera to the model. If the user is no admin he can only access owned resourced if he is an admin he may access everything.
     * @param $model
     * @param PrettusRepositoryInterface $repository
     * @return mixed
     */
    public function apply($model, PrettusRepositoryInterface $repository)
    {
        $diffInDays=Carbon::now()->subDays($this->days)->toDateTimeString();
        $retQuery= $model->where($this->column , '<=', $diffInDays);

        return $retQuery;

    }

}
