<?php
/**
 * User: fabianwidmann
 * Date: 15.09.17
 * Time: 11:26
 */

namespace App\Containers\WeatherData\Exceptions;


use Exception;

/**
 * Class RetrievalFailedException
 * @package App\Containers\WeatherData\Exceptions
 * @author Fabian Widmann <fabian.widmann@gmail.com>
 * This exception signals a fatal error in one of the library which will halt the queued job and fails it immediately.
 */
class RetrievalFailedException extends Exception
{
    protected $source;

    public function __construct($message,string $source,  $code = 0, Exception $previous = null) {
        $this->source=$source;
        parent::__construct($message, $code, $previous);
    }

    /**
     * @return string
     */
    public function getSource(): string
    {
        return $this->source;
    }



}