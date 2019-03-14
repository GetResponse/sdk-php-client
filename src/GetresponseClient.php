<?php

namespace Getresponse\Sdk\Client;

use Getresponse\Sdk\Client\Authentication\AuthenticationProvider;
use Getresponse\Sdk\Client\Debugger\Logger;
use Getresponse\Sdk\Client\Environment\Environment;
use Getresponse\Sdk\Client\Handler\Call\CallRegistry;
use Getresponse\Sdk\Client\Handler\RequestHandler;
use Getresponse\Sdk\Client\Operation\OperationResponseCollector;
use Getresponse\Sdk\Client\Operation\Operation;
use Getresponse\Sdk\Client\Operation\OperationResponse;
use Getresponse\Sdk\Client\Operation\OperationResponseCollection;
use Getresponse\Sdk\Client\Operation\OperationResponseFactory;
use GuzzleHttp\Psr7\Request;

/**
 * Class GetresponseClient
 * @package Getresponse\Sdk\Client
 */
class GetresponseClient
{
    /**
     * @var RequestHandler
     */
    private $requestHandler;

    /**
     * @var Logger
     */
    private $logger;

    /**
     * @var Environment
     */
    private $environment;

    /**
     * @var AuthenticationProvider
     */
    private $authentication;

    /**
     * GetresponseClient constructor.
     * @param RequestHandler $requestHandler
     * @param Environment $environment
     * @param AuthenticationProvider $authentication
     */
    public function __construct(
        RequestHandler $requestHandler,
        Environment $environment,
        AuthenticationProvider $authentication
    ) {
        $this->requestHandler = $requestHandler;
        $this->environment = $environment;
        $this->authentication = $authentication;
    }

    /**
     * @param Logger $logger
     */
    public function setLogger(Logger $logger)
    {
        $this->logger = $logger;
        $this->requestHandler->setLogger($logger);
    }

    /**
     * @param Operation $operation
     * @return OperationResponse
     */
    public function call(Operation $operation)
    {
        $callRegistry = new CallRegistry();
        $request = $this->createRequest($operation);
        $request = $this->authentication->authenticate($request);

        $request = $this->environment->processRequest($request);

        $callRegistry->registerRequest($request, $operation->getSuccessCode());
        $call = $callRegistry->getCurrent();
        
        $this->requestHandler->send($call);
        return OperationResponseFactory::createByCall($call);
    }

    /**
     * @param Operation[] $operations
     * @return OperationResponseCollection | OperationResponse[]
     */
    public function callMany(array $operations)
    {
        $callRegistry = new CallRegistry();
        foreach ($operations as $operation) {
            $request = $this->createRequest($operation);
            $request = $this->authentication->authenticate($request);
            $request = $this->environment->processRequest($request);
            $callRegistry->registerRequest($request, $operation->getSuccessCode());
        }

        $collector = new OperationResponseCollector();
        $this->requestHandler->sendMany($callRegistry);
        foreach ($callRegistry as $call) {
            $collector->collect(OperationResponseFactory::createByCall($call));
        }
        return $collector->getCollection();
    }

    /**
     * @param string $url
     * @return string
     */
    private function buildUri($url)
    {
        return rtrim($this->environment->getUrl(), '/') . $url;
    }

    /**
     * @param Operation $operation
     * @return Request
     */
    private function createRequest(Operation $operation)
    {
        return new Request(
            $operation->getMethod(),
            $this->buildUri($operation->getUrl()),
            ['User-Agent' => UAResolver::resolve($this->requestHandler, $operation)],
            $operation->getBody()
        );
    }
}
