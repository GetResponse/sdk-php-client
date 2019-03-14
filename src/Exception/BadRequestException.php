<?php
namespace Getresponse\Sdk\Client\Exception;

/**
 *
 * Request for response code = 400
 *
 * Class BadRequestException
 * @package Getresponse\Sdk\Client\Exception
 */
class BadRequestException extends ClientException
{
    const BAD_REQUEST_MESSAGE = 'Bad request, The request could not be understood by the server due to malformed syntax. The client SHOULD NOT repeat the request without modifications. ';

    /**
     * @return string
     */
    protected function getErrorMessage()
    {
        return self::BAD_REQUEST_MESSAGE . parent::CLIENT_ERROR_MSG;
    }
}
