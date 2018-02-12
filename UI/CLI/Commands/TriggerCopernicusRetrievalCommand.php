<?php
/**
 * User: fabianwidmann
 * Date: 07.09.17
 * Time: 10:23
 */

namespace App\Containers\WeatherData\UI\CLI\Commands;


use App\Ship\Parents\Commands\ConsoleCommand;
use Carbon\Carbon;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

/**
 * Class TriggerCopernicusRetrievalCommand
 * @package App\Containers\WeatherData\UI\CLI\Commands
 * @author Fabian Widmann <fabian.widmann@gmail.com>
 *
 * Can be used to trigger the download of the newest ECMWF data available - currently unused
 */
class TriggerCopernicusRetrievalCommand extends ConsoleCommand
{
    protected $signature = 'weather:triggerECMWFDownload';

    protected $description = 'Trigger the download of the ecmwf api.';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $client = new Client();
        Log::info(config('weather.copernicus_host'));
        $url = config('weather.copernicus_host');
        $res = $client->request('GET', $url . '/retrieve', [
            'query' => ['timestamp' => Carbon::now()->subDay(5)->toIso8601String()]
        ]);
//        $res = $client->get(?timestamp='.Carbon::now()->toIso8601String());
        $body = \GuzzleHttp\json_decode($res->getBody());
        Log::info($res->getStatusCode() . ": response=" . print_r($body, true));
        if (!isset($body->file_name))
            //todo: queue job - but should not happen.
            Log::info($res->getStatusCode() . ": In Progress or not available -  response=" . $body->message);

        return;

    }
}