<?php

namespace App\Containers\WeatherData\Exceptions;

use App\Ship\Parents\Exceptions\Exception;
use Symfony\Component\HttpFoundation\Response;

class WeatherDataNotFound extends Exception
{
    public $httpStatusCode = Response::HTTP_BAD_REQUEST;
    public $message = 'Could not find the requested data in our database.';

}
