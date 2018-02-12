<?php

namespace App\Containers\WeatherData\Providers;

use Barryvdh\Cors\ServiceProvider;
use Illuminate\Queue\Events\JobFailed;
use Illuminate\Queue\Events\JobProcessed;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Queue;


class MainServiceProvider extends ServiceProvider
{
    public function boot()
    {
        //set the php.ini to use the new precision mode source: https://stackoverflow.com/a/43056278/1496040 RFC: https://wiki.php.net/rfc/precise_float_value
        ini_set( 'serialize_precision', -1 );
        Queue::after(function (JobProcessed $event) {
            $command = unserialize(\GuzzleHttp\json_decode($event->job->getRawBody())->data->command);
//            if(get_class($command) == DataRetrievalJob::class) {  al
//                Log::info("Queue finished job... geoLocationId=" . $command->getProcessedId());
//            }
        });
        Queue::failing(function (JobFailed $event) {
            // $event->connectionName
            // $event->job
            // $event->exception
            Log::alert("Error" . $event->job->getRawBody());
        });
    }

}
