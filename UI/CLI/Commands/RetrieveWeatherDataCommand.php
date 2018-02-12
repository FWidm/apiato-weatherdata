<?php
/**
 * User: fabianwidmann
 * Date: 07.09.17
 * Time: 10:23
 */

namespace App\Containers\WeatherData\UI\CLI\Commands;


use App\Containers\GeoLocation\Data\Repositories\GeoLocationRepository;
use App\Containers\WeatherData\Data\Criterias\OlderThanDateCriteria;
use App\Containers\WeatherData\Gateways\AbstractDataRetrievalGateway;
use App\Containers\WeatherData\Jobs\DataRetrievalJob;
use App\Ship\Criterias\Eloquent\OrderByCreationDateAscendingCriteria;
use App\Ship\Criterias\Eloquent\ThisEqualThatCriteria;
use App\Ship\Parents\Commands\ConsoleCommand;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;

/**
 * Class RetrieveWeatherData
 * @package App\Containers\WeatherData\UI\CLI\Commands
 * @author Fabian Widmann <fabian.widmann@gmail.com>
 *
 * This job can be triggered in a timed interval and it will schedule  various amount of queueable jobs.
 * - Queued Job will retrieve data from all available sources with the help of various GateWays
 * - Number of jobs can be modified in the config, see $itemCount
 */
class RetrieveWeatherDataCommand extends ConsoleCommand
{
    protected $signature = 'weather:retrieve';

    protected $description = 'Retrieve Files.';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $itemCount = config('weather.retrieved_models_per_cycle');
        $maxGatewayDelay = max(array_map(function (AbstractDataRetrievalGateway $gateway) {
            if (is_int($gateway->getTimeDelayInDays()))
                return $gateway->getTimeDelayInDays();
            else
                throw new \InvalidArgumentException("Time delay set in gateway is not an integer. v=" . $gateway->getTimeDelay() .
                    ", gateway=" . get_class($gateway));
        }, config('weather.gateways')));

//        $delayInDays = max(config('weather.retrieval_delay_in_days'));

        Log::info("Filtering by geolocations older than $maxGatewayDelay days and limiting to $itemCount");
        //Filter all geolocations - retrieve only geolocs older than the currently supported minimum wait time and order them by age ascending.
        $resultSet = App::make(GeoLocationRepository::class)->pushCriteria(new OlderThanDateCriteria('request_timestamp', $maxGatewayDelay))
            ->pushCriteria(new ThisEqualThatCriteria("executed", false))
            ->pushCriteria(new OrderByCreationDateAscendingCriteria())->paginate($itemCount);
        Log::info("Retrieved " . count($resultSet) . " unexecuted geolocations...");
        //query up jobs for each geolocation
        foreach ($resultSet as $geoLocation) {
            Log::info("Processing... geoloc with id=" . $geoLocation->id . ";  " . $geoLocation);
            DataRetrievalJob::dispatch($geoLocation);
        }
    }
}