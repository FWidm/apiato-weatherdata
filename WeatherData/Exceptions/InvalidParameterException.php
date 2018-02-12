<?php

namespace App\Containers\WeatherData\Exceptions;

use App\Ship\Parents\Exceptions\Exception;
use Exception as BaseException;
use Symfony\Component\HttpFoundation\Response;

class InvalidParameterException extends Exception
{
    public $httpStatusCode = Response::HTTP_BAD_REQUEST;
    public $message = 'Specified parameter or parameter value is incorrect.';

}
