<?php

namespace Getresponse\Sdk\Client\Handler\Call;

use Psr\Http\Message\RequestInterface;
use Traversable;

/**
 * Class CallRegistry
 * @package Getresponse\Sdk\Client\Handler\Call
 */
class CallRegistry implements \IteratorAggregate, \Countable
{
    /**
     * @var array
     */
    private static $requestRegistry = [];
    
    /** @var array | Call */
    protected $calls = [];
    
    /**
     * @param RequestInterface $request
     * @param int $successCode
     */
    public function registerRequest(RequestInterface $request, $successCode)
    {
        $requestIdentifier = spl_object_hash($request);
        self::$requestRegistry[$requestIdentifier] = $request;
        
        $this->registerCall($requestIdentifier, new Call($request, $successCode, $requestIdentifier));
    }
    
    /**
     * @param string $identifier
     * @param Call $call
     */
    public function registerCall($identifier, Call $call)
    {
        $this->calls[$identifier] = $call;
    }
    
    /**
     * @return \ArrayIterator | Call[]
     */
    public function getIterator(): Traversable
    {
        return new \ArrayIterator($this->calls);
    }
    
    /**
     * @return Call
     */
    public function getCurrent()
    {
        return current($this->calls);
    }
    
    /**
     * @return Call
     */
    public function getLast()
    {
        return end($this->calls);
    }
    
    /**
     * @param string $requestIdentifier
     * @return Call
     */
    public function get($requestIdentifier)
    {
        return $this->calls[$requestIdentifier];
    }
    
    /**
     * @param string $requestIdentifier
     * @return boolean
     */
    public function has($requestIdentifier)
    {
        return !empty($this->calls[$requestIdentifier]);
    }

    public function count(): int
    {
        return count($this->calls);
    }
}