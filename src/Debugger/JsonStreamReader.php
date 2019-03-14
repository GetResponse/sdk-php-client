<?php
namespace Getresponse\Sdk\Client\Debugger;

use Psr\Http\Message\StreamInterface;

/**
 * Class JsonStreamReader
 * @package Getresponse\Sdk\Client\Debugger
 */
class JsonStreamReader implements StreamReader
{
    /** @var int */
    public static $maxRecursionDepthForResponse = 512;
    
    /**
     * @inheritDoc
     */
    public function read(StreamInterface $stream)
    {
        if (!extension_loaded('json')) {
            throw StreamReaderException::readerExecution('JSON extension not found.');
        }
        $responseBody = $stream->getContents();
        if (is_string($responseBody)) {
            $content = json_decode($responseBody, true, self::$maxRecursionDepthForResponse);
            if (JSON_ERROR_NONE !== json_last_error()) {
                throw StreamReaderException::unreadableBody(json_last_error_msg());
            }
            return $content;
        }
    }
}