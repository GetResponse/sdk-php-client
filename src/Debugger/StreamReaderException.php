<?php
namespace Getresponse\Sdk\Client\Debugger;

use Getresponse\Sdk\Client\Exception\BaseException;

/**
 * Class StreamReaderException
 * @package Getresponse\Sdk\Client\Debugger
 */
class StreamReaderException extends BaseException
{
    const EXECUTION_ERROR_CODE = 10;
    const UNREADABLE_BODY_ERROR_CODE = 100;
    
    /**
     * @param string $message
     * @return StreamReaderException
     */
    public static function readerExecution($message)
    {
        return new self($message, self::EXECUTION_ERROR_CODE);
    }
    
    /**
     * @param string $message
     * @return StreamReaderException
     */
    public static function unreadableBody($message)
    {
        return new self($message, self::UNREADABLE_BODY_ERROR_CODE);
    }
}