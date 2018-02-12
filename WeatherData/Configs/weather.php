<?php

use App\Containers\WeatherData\Gateways\CopernicusRetrievalGateway;
use App\Containers\WeatherData\Gateways\DWDRetrievalGateway;

return [
    /*******************************************************************************************************************
     * Requests
     ******************************************************************************************************************/
    'request_convert_string' => 'convert',
    'request_byParticipantId_string' => 'participantId',
    'request_typeLikeThat_string' => 'type',
    'request_srcLikeThat_string' => 'source',
    'request_from_string' => 'from',
    'request_to_string' => 'to',
    /**
     * e.g. type=%x;%y%;z => find everything that has an x at the end or contains an y or has the type z.
     * see: https://stackoverflow.com/questions/2163803/what-is-the-semicolon-reserved-for-in-urls/2163885#2163885
     */
    'request_item_separator' => ';',
    /**
     * Separates variables inside of an item like this: item=a,b,c => a b c are parsed as single values
     */
    'request_item_value_separator' => ',',
    /**
     * Separates variables to convert from the target unit: *:C converts all applicable variables to Celsius
     * see: https://stackoverflow.com/questions/2053132/is-a-colon-safe-for-friendly-url-use for info
     */
    'request_conversion_unit_separator' => ':',
    /**
     * Wildcard for the conversion request -> converts everything that is applicable to the given unit
     */
    'request_wildcard' => '*',

    /**
     * Accepted Date formats when using 'from' & 'to'
     */
    'accepted_date_formats' => [
        'Y', // year only
        'Y-m', // no day
        'Y-m-d', //no time
        'Y-m-d\TH', //no minutes
        'Y-m-d\TH:i', //no seconds
        'Y-m-d\TH:i:s', //no timezone
        // Iso
        DateTime::ATOM,
    ],

    /*******************************************************************************************************************
     * DB
     ******************************************************************************************************************/
    'weather_data_table_name' => 'weather_data',

    /*******************************************************************************************************************
     * Gateways
     ******************************************************************************************************************/
    'retrieved_models_per_cycle' => 3,

    /**
     * Specify data availability here:
     * DWD data will be fully available after 1 day
     * ECMWF data will be fully available after 5 days
     * Add another source here if you extend the functionality.
     */
    //todo remove
    'retrieval_delay_in_days' => [
        'dwd' => 1,
        'copernicus-era-interim' => 5,
    ],


    /**
     * Register all needed gateways here by adding a new instance for each one.
     */
    'gateways' => [
        'dwd' => new DWDRetrievalGateway(),
        'copernicus' => new CopernicusRetrievalGateway()
    ],
    /**
     * Retrieval Job Duration in seconds
     */
    'retrieval_job_max_duration' => 90,

    /**
     * DWD related settings
     */
    'dwd_storage_path' => storage_path(),

    /**
     * Copernicus settings
     */
    'copernicus_host' => '0.0.0.0:5055',


];
