<?php

/**
 * @apiGroup           WeatherData
 * @apiName            indexWeatherData
 *
 * @api                {GET} /v1/weatherdata Endpoint title here..
 * @apiDescription     Endpoint description here..
 *
 * @apiVersion         1.0.0
 * @apiPermission      none
 *
 * @apiParam           {String}  [source] - find all data coming from a source - supports wildcards (*)
 * @apiParam           {String}  [type] - find all data that contain the gieven type - supports wildcards (*) e.g. '*temp*' => anything which contains 'temp'
 * @apiParam           {String}  [from] - start date-range supports year, year-month, up until full ISO8601. When only the year is specified it means today in year x.
 * @apiParam           {String}  [to] - end date-range: supports year, year-month, up until full ISO8601. When only the year is specified it means today in year x.
 * @apiParam           {String}  [participantId] - obtain data from a single participant.
 *
 * @apiSuccessExample  {json}  Success-Response (paginated):
 * HTTP/1.1 200 OK
 * {
 * {
 * {
 * "data": [
 * {
 * "type": "WeatherData",
 * "id": "4jkoag8xdpvzel7b",
 * "attributes": {
 * "object": "WeatherData",
 * "created_at": {
 * "date": "2017-10-05 14:50:13.000000",
 * "timezone_type": 3,
 * "timezone": "UTC"
 * },
 * "updated_at": {
 * "date": "2017-10-05 14:50:13.000000",
 * "timezone_type": 3,
 * "timezone": "UTC"
 * },
 * "source_id": 1,
 * "source": "DWD",
 * "value": "0.000000",
 * "description": {
 * "name": "SD_SO: hourly sunshine duration in minutes.",
 * "units": "min",
 * "quality": 1,
 * "qualityType": "QN_7: quality level, see @ ftp://ftp-cdc.dwd.de/pub/CDC/observations_germany/climate/hourly/sun/recent/DESCRIPTION_obsgermany_climate_hourly_sun_recent_en.pdf,"
 * },
 * "classification": "Atmosphere",
 * "distance": "8.122990",
 * "lat": "48.385100",
 * "lon": "9.483700",
 * "date": {
 * "date": "2017-09-28 00:00:00.000000",
 * "timezone_type": 3,
 * "timezone": "UTC"
 * },
 * "type": "sunshine duration",
 * "geo_location_id": "ze6bqg8wlv3r7pan",
 * "unit": "min",
 * "real_id": 89
 * },
 * "relationships": {
 * "source": {
 * "data": {
 * "type": "WeatherSource",
 * "id": "zwn0ydvezv4kg9jx"
 * }
 * }
 * }
 * }
 * ],
 * "included": [
 * {
 * "type": "WeatherSource",
 * "id": "zwn0ydvezv4kg9jx",
 * "attributes": {
 * "object": "WeatherSource",
 * "created_at": {
 * "date": "2017-10-05 14:50:13.000000",
 * "timezone_type": 3,
 * "timezone": "UTC"
 * },
 * "updated_at": {
 * "date": "2017-11-07 15:05:06.000000",
 * "timezone_type": 3,
 * "timezone": "UTC"
 * },
 * "original_id": 3402,
 * "source": "DWD",
 * "data": {
 * "id": "03402",
 * "from": "2002-11-01T15:05:02+00:00",
 * "name": "Münsingen-Apfelstetten",
 * "state": "Baden-Württemberg",
 * "until": "2017-11-05T15:05:02+00:00",
 * "active": true,
 * "height": "750",
 * "latitude": "48.3851",
 * "longitude": "9.4837"
 * },
 * "real_id": 1
 * }
 * },
 * ],
 * "meta": {
 * "include": [],
 * "custom": [],
 * "pagination": {
 * "total": 209,
 * "count": 15,
 * "per_page": 15,
 * "current_page": 1,
 * "total_pages": 14
 * }
 * },
 * "links": {
 * "self": "http://api.apiato.dev/v1/weatherData?source=DWD&page=1",
 * "first": "http://api.apiato.dev/v1/weatherData?source=DWD&page=1",
 * "next": "http://api.apiato.dev/v1/weatherData?source=DWD&page=2",
 * "last": "http://api.apiato.dev/v1/weatherData?source=DWD&page=14"
 * }
 * }
 * }
 */

$router->get('environmental-data', [
    'uses' => 'Controller@index',
    'middleware' => [
        'auth:api',
    ],
]);
