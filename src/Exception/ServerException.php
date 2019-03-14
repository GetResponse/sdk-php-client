<?php
namespace Getresponse\Sdk\Client\Exception;

/**
 * Exception for response codes > 499
 *
 * Class ServerException
 * @package Getresponse\Sdk\Client\Exception
 */
class ServerException extends RequestException
{
    const SERVER_ERROR_MSG = 'server error: server is temporarily unavailable, please contact our support. Please see response body for request uuid and paste it in support ticket, this uuid will help us find Your request in our logs';

    /**
     * @return string
     */
    protected function getExceptionSpecificMessage()
    {
        return self::SERVER_ERROR_MSG;
    }
}