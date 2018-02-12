<?php

namespace App\Containers\WeatherData\Jobs;

use App\Containers\GeoLocation\Data\Repositories\GeoLocationRepository;
use App\Containers\GeoLocation\Models\GeoLocation;
use App\Containers\WeatherData\Data\Repositories\WeatherDataRepository;
use App\Containers\WeatherData\Data\Repositories\WeatherSourceRepository;
use App\Containers\WeatherData\Exceptions\RetrievalFailedException;
use App\Containers\WeatherData\Gateways\AbstractDataRetrievalGateway;
use App\Ship\Parents\Jobs\Job;
use Exception;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DataRetrievalJob extends Job
{
//    public $tries = 10;

    protected $geoLocation;
    protected $weatherDataArray;
    protected $additionalDataArray;

    /**
     * Create a new Job to work on a given GeoLocation
     * DataRetrievalJob constructor.
     * @param GeoLocation $geoLocation
     */
    public function __construct($geoLocation)
    {
        $this->geoLocation = $geoLocation;
        $this->weatherDataArray = [];
        $this->additionalDataArray = [];
    }

    /**
     * Determine the time at which the job should timeout.
     *
     * @return \DateTime
     */
    public function retryUntil()
    {
        $seconds = config('weather.retrieval_job_max_duration');
        Log::info("DataRetrievalJob: job max duration=" . $seconds);
        return now()->addSeconds($seconds);
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        //check if it got executed to be sure
        if ($this->geoLocation->executed) {
            Log::info("DataRetrievalJob: Data already exists.");
            return;
        }
        Log::info("DataRetrievalJob: retrieving geoloc with id=" . $this->geoLocation->id . "; lat=" . $this->geoLocation->lat . ", lon=" . $this->geoLocation->lon);
        try {
            $retHelpers = config('weather.gateways');

            $this->triggerRetrievalHelpers($retHelpers);
            //--- DB Transaction
            // dump data into the DB, mark geolocation as executed
            // Transformed data from the gateways comes in the form of an array of this type:
            // obj -> [data -> [attributes: named array]]
            //todo: introduce flag to do a transaction for each data source instead of having ones that pertains all sources.
            DB::transaction(function () {
                //todo: additional info.
                foreach ($this->additionalDataArray as $additionalData) {
                    $dataArray = $additionalData['data'];
                    $weatherSource = App::make(WeatherSourceRepository::class)->findWhere(
                        $this->buildWhereQueryArray($dataArray['original_id'], $dataArray['source']))->first();

                    //createOrUpdate does not work here as the station id is not used as weather source id. We need to query it specifically
                    if (!$weatherSource)
                        App::make(WeatherSourceRepository::class)->create($dataArray);
                    else {
                        App::make(WeatherSourceRepository::class)->update($dataArray, $weatherSource->id);
                    }
                }
                foreach ($this->weatherDataArray as $weatherData) {
                    $weatherDataArray = $weatherData['data'];
                    // check if there is a valid source for this weatherData object.
                    $weatherSource = App::make(WeatherSourceRepository::class)->findWhere(
                        $this->buildWhereQueryArray($weatherDataArray['source_id'], $weatherDataArray['source']))->first();
                    //if the weathersource exists and has an id, we set the source_id to our internal id, else we set it to null.
                    $weatherDataArray['source_id'] = isset($weatherSource->id) ? $weatherSource->id : null;
                    App::make(WeatherDataRepository::class)->create($weatherDataArray);
                    // mark the geolocation as executed
                    App::make(GeoLocationRepository::class)->update(['executed' => true], $this->geoLocation->id);
                }
            }, 5);
            Log::info("DataRetrievalJob: Job successfully completed.");
        } catch (RetrievalFailedException $e) {
            Log::info("DataRetrievalJob: Retrieval failed. message=" . $e->getMessage() . "; src=" . $e->getSource());
            $this->fail($e);
        }
    }

    /** Fetch data from all the given helpers
     * @param array $retHelpers one or more RetrievalHelpers for various DataSources
     */
    private function triggerRetrievalHelpers(array $retHelpers)
    {

        foreach ($retHelpers as $retHelper) {
            /* @var $retHelper AbstractDataRetrievalGateway */
            [$weatherDataArr, $sourceArr] = $retHelper->getData($this->geoLocation->lat,
                $this->geoLocation->lon, $this->geoLocation->request_timestamp, $this->geoLocation->id);
            $this->weatherDataArray = array_merge($this->weatherDataArray, $weatherDataArr);
            $this->additionalDataArray = array_merge($this->additionalDataArray, $sourceArr);
        }
    }
    private function buildWhereQueryArray($original_id, $source)
    {
        return ['original_id' => $original_id, 'source' => $source];
    }

    public function getProcessedId()
    {
        return $this->geoLocation->id;
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
