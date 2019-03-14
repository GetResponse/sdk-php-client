<?php

namespace Getresponse\Sdk\Client\Exception;

/**
 * Class MalformedResponseDataException
 * @package Getresponse\Sdk\Client\Exception
 */
class MalformedResponseDataException extends BaseException
{
    /**
     * @param string $jsonString
     * @return MalformedResponseDataException
     */
    public static function createFromJsonLastErrorMsg($jsonString)
    {
        return new self(
            vsprintf(
                'Invalid JSON: %s (%d) Data: %s',
                [json_last_error_msg(), json_last_error(), $jsonString]
            )
        );
    }
}
