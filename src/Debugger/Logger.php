<?php
namespace Getresponse\Sdk\Client\Debugger;

use Getresponse\Sdk\Client\Handler\Call\Call;
use Getresponse\Sdk\Client\Handler\Call\CallInfo;
use Psr\Http\Message\RequestInterface;
use Psr\Log\LoggerInterface;

/**
 * Class Logger
 * @package Getresponse\Sdk\Client\Debugger
 */
class Logger
{
    /** @var LoggerInterface */
    private $logger;
    
    /**
     * Logger constructor.
     * @param LoggerInterface $logger
     */
    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }
    
    /**
     * @param Call $call
     * @param CallInfo | null $info
     */
    public function debugCall(Call $call, CallInfo $info = null)
    {
        $message = $call->hasException()
            ? 'Thrown exception: ' . $call->getException()->getMessage()
            : 'Received: ' . $call->getResponse()->getStatusCode();
        
        $this->logger->debug(
            $message,
            [
                'response' => $call->getResponse(),
                'request' => $call->getRequest(),
                'info' => $info,
            ]
        );
    }
    
    /**
     * @param RequestInterface $request
     */
    public function debugRequest(RequestInterface $request)
    {
        $this->logger->debug(
            vsprintf(
                'Sending: %s %s',
                [$request->getMethod(), $request->getUri()]
            ),
            ['request' => $request]
        );
    }
}