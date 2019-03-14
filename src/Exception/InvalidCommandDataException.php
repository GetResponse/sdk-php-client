<?php

namespace Getresponse\Sdk\Client\Exception;

/**
 * Class InvalidCommandDataException
 * @package Getresponse\Sdk\Client\Exception
 */
class InvalidCommandDataException extends BaseException
{
    /**
     * @param array $data
     * @return InvalidCommandDataException
     */
    public static function createFromJsonLastErrorMsg(array $data)
    {
        return new self(
            vsprintf(
                'Invalid data: %s (%d) Dump: %s',
                [json_last_error_msg(), json_last_error(), print_r($data, true)]
            )
        );
    }

    /**
     * @param string $key
     * @param string $commandName
     * @return InvalidCommandDataException
     */
    public static function createFromInvalidField($key, $commandName)
    {
        return new self(
            vsprintf('Field %s is not available for command %s', [$key, $commandName])
        );
    }

    /**
     * @param array $missingFields
     * @param string $commandName
     * @return InvalidCommandDataException
     */
    public static function createFromMissingFieldsList(array $missingFields, $commandName)
    {
        return new self(
            vsprintf('Command %s is missing required fields: %s', [$commandName, implode(', ', $missingFields)])
        );
    }
}
