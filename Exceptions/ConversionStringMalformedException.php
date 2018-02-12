<?php
/**
 * User: fabianwidmann
 * Date: 15.09.17
 * Time: 11:26
 */

namespace App\Containers\WeatherData\Exceptions;


use Exception;

/**
 * Class DataNotAvailableException
 * @package App\Containers\WeatherData\Exceptions
 * @author Fabian Widmann <fabian.widmann@gmail.com>
 *
 * This exception should be used to indicate that no data could be retrieved but the job can proceed.
 * E.g. Queried location is outside of germany will lead to the DWD Trait to throw this exception
 * while still performing the job to query data from the other sources.
 */
class ConversionStringMalformedException extends Exception
{

    public function __construct($message,$code = 0, Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }


}