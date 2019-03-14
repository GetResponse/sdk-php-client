<?php
namespace Getresponse\Sdk\Client\Exception;

/**
 *
 * Exception for resonse code = 401
 *
 * Class UnauthorizedException
 * @package Getresponse\Sdk\Client\Exception
 */
class UnauthorizedException extends ClientException
{
    const CLIENT_ERROR_UNAUTHORIZED_MSG = 'client error: unauthorized, please check Your authorization headers, make sure that account is active and has api access enabled';

    /**
     * @return string
     */
    protected function getExceptionSpecificMessage()
    {
        return self::CLIENT_ERROR_UNAUTHORIZED_MSG;
    }

}