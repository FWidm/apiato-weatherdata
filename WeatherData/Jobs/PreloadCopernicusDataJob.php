<?php

namespace App\Containers\WeatherData\Jobs;

use Exception;
use App\Ship\Parents\Jobs\Job;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;


class PreloadCopernicusDataJob extends Job
{

//    public $tries = 10;

    protected $copernicusServerHost;


    public function __construct($host)
    {
        $this->copernicusServerHost=$host;
    }

    /**
     * Determine the time at which the job should timeout.
     *
     * @return \DateTime
     */
    public function retryUntil()
    {
        $seconds = config('weather.copernicus_preload_job_max_duration');
        Log::info("PreloadCopernicusDataJob: job max duration=" . $seconds);
        return now()->addSeconds($seconds);
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Log::info("PreloadCopernicusDataJob: starting at " . Carbon::now()->toIso8601String());
//        $copernicus=config('weather.gateways.copernicus');
//
//        var_dump($copernicus);
    }


    /**
     * The job failed to process.
     *
     * @param  Exception $exception
     * @return void
     */
    public function failed(Exception $exception)
    {
    }


}
