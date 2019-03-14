<?php

namespace Getresponse\Sdk\Client\Handler;

use Getresponse\Sdk\Client\Debugger\Logger;
use Getresponse\Sdk\Client\Handler\Call\Call;
use Getresponse\Sdk\Client\Handler\Call\CallRegistry;

/**
 * Interface RequestHandler
 * @package Getresponse\Sdk\Client\Handler
 */
interface RequestHandler
{
    /**
     * @param Call $call
     */
    public function send(Call $call);

    /**
     * @param CallRegistry $callRegistry
     */
    public function sendMany(CallRegistry $callRegistry);

    /**
     * @param Logger $logger
     * @return void
     */
    public function setLogger(Logger $logger);
    
    /**
     * @return string
     */
    public function getUAString();
}
