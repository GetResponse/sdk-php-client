<?php
namespace Getresponse\Sdk\Client\Handler;

use Getresponse\Sdk\Client\Exception\BaseException;

/**
 * Class ParseResponseException
 * @package Getresponse\Sdk\Client\Handler
 */
class ParseResponseException extends BaseException
{
    /**
     * @param \Exception $previousException
     * @return static
     */
    public static function create(\Exception $previousException)
    {
        return new static('Invalid response body.', 0, $previousException);
    }
}