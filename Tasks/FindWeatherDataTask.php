<?php

namespace App\Containers\WeatherData\Tasks;

use App\Containers\WeatherData\Data\Criterias\BelongsToUserCriteria;
use App\Containers\WeatherData\Data\Repositories\WeatherDataRepository;
use App\Containers\WeatherData\Exceptions\WeatherDataNotFound;
use App\Ship\Parents\Tasks\Task;
use Illuminate\Support\Facades\Log;
use Exception;


class FindWeatherDataTask extends Task
{


    private  $repository;

    public function __construct(WeatherDataRepository $repository)
    {
        $this->repository=$repository;
    }

    public function run($id)
    {
        try {
            $data = $this->repository->find($id);
            Log::info($data);
        } catch (Exception $e) {
            throw new WeatherDataNotFound();
        }
        return $data;

    }

    public function filterByAuthUser($id)
    {
        Log::info("auth user=" . $id);
        $this->repository->pushCriteria(new BelongsToUserCriteria($id));
    }

}
