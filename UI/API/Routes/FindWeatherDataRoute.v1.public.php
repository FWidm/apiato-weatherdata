<?php

/**
 * @apiGroup           WeatherData
 * @apiName            show
 *
 * @api                {GET} /v1/weatherdata/:id Endpoint title here..
 * @apiDescription     Endpoint description here..
 *
 * @apiVersion         1.0.0
 * @apiPermission      none
 *
 * @apiParam           {String}  parameters here..
 *
 * @apiSuccessExample  {json}  Success-Response:
 * HTTP/1.1 200 OK
{
  // Insert the response of the request here...
}
 */

$router->get('environmental-data/{id}', [
    'uses'  => 'Controller@show',
    'middleware' => [
      'auth:api',
    ],
]);
