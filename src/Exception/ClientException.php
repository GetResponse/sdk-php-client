<?php
namespace Getresponse\Sdk\Client\Exception;

use Psr\Http\Message\ResponseInterface;

/**
 *
 * Exception for response codes > 399
 *
 * Class ClientException
 * @package Getresponse\Sdk\Client\Exception
 */
class ClientException extends RequestException
{
    const CLIENT_ERROR_MSG = 'client error, please check that Your request is correct, check Your url, headers and payload. See response body for validation details';

    /**
     * @return string
     */
    protected function getExceptionSpecificMessage()
    {
        return self::CLIENT_ERROR_MSG;
    }
}