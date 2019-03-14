<?php
namespace Getresponse\Sdk\Client;

use Getresponse\Sdk\Client\Handler\RequestHandler;
use Getresponse\Sdk\Client\Operation\OperationVersionable;

/**
 * Class UAResolver
 * @package Getresponse\Sdk\Client
 */
class UAResolver
{
    /**
     * @param RequestHandler $handler
     * @param OperationVersionable $operation
     * @return string
     */
    public static function resolve(RequestHandler $handler, OperationVersionable $operation)
    {
        return sprintf(
            '%s GetResponse-Client/%s %s (%s)',
            $operation->getVersion(),
            Version::VERSION ,
            self::getHandlerName($handler),
            $handler->getUAString()
        );
    }
    
    /**
     * @param RequestHandler $handler
     * @return string
     */
    private static function getHandlerName(RequestHandler $handler)
    {
        $parts = explode('\\', get_class($handler));
        return end($parts);
    }
}