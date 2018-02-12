<?php

namespace App\Containers\WeatherData\Data\Criterias;

use App\Ship\Parents\Criterias\Criteria;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\Log;
use Prettus\Repository\Contracts\RepositoryInterface as PrettusRepositoryInterface;

class ThisLikeThatCriteria extends Criteria
{

    /**
     * @var
     */
    private $field;

    /**
     * @var
     */
    private $values;


    public function __construct($field, $values)
    {
        $this->field = $field;
        $this->values = $values;
    }

    /**
     * Applies the criteria - if more than one value is separated by the configured separator we will "OR" all the params.
     * @param Builder $model
     * @param \Prettus\Repository\Contracts\RepositoryInterface $repository
     * @return  mixed
     */
    public function apply($model, PrettusRepositoryInterface $repository)
    {


//        $model=$model->where($this->field,'LIKE', str_replace(config('weather.request_wildcard'),'%',array_shift($values)));
        $model=$model->where(function($query) {
            $values=explode(config('weather.request_item_separator'),$this->values);
            Log::info("vals".print_r($values,true));

            $query->where($this->field,'LIKE', str_replace(config('weather.request_wildcard'),'%',array_shift($values)));
            foreach ($values as $value)
                $query->orWhere($this->field,'LIKE', str_replace(config('weather.request_wildcard'),'%',$value));
        });

        return $model;
    }

}