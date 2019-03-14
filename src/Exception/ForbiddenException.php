<?php
namespace Getresponse\Sdk\Client\Exception;

/**
 *
 * Exception for response code = 403
 *
 * Class ForbiddenException
 * @package Getresponse\Sdk\Client\Exception
 */
class ForbiddenException extends ClientException
{
    const FORBIDDEN_ERROR_MSG = 'client error: forbidden, please check Your access rights';

    /**
     * @return string
     */
    protected function getExceptionSpecificMessage()
    {
        return self::FORBIDDEN_ERROR_MSG;
    }
}