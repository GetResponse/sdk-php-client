<?php

namespace Getresponse\Sdk\Client\Handler\Call;

use Getresponse\Sdk\Client\Exception\RequestException;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * Class Call
 * @package Getresponse\Sdk\Client\Handler\Call
 */
class Call
{
    /** @var string */
    private $id;
    
    /** @var int */
    private $successCode;
    
    /** @var RequestInterface */
    private $request;
    
    /** @var ResponseInterface | null */
    private $response;
    
    /** @var RequestException | null */
    private $exception;
    
    /**
     * Call constructor.
     * @param RequestInterface $request
     * @param int $successCode
     * @param string | null $id
     */
    public function __construct(RequestInterface $request, $successCode, $id = null)
    {
        $this->request = $request;
        $this->successCode = $successCode;
        $this->id = null !== $id ? $id : uniqid();
    }
    
    /**
     * @return string
     */
    public function getIdentifier()
    {
        return $this->id;
    }
    
    /**
     * @return int
     */
    public function getSuccessCode()
    {
        return $this->successCode;
    }
    
    /**
     * @return RequestInterface
     */
    public function getRequest()
    {
        return $this->request;
    }
    
    /**
     * @param ResponseInterface $response
     */
    public function setResponse(ResponseInterface $response)
    {
        if ($this->hasException()) {
            throw new \RuntimeException('Invalid state. Call has an exception within.');
        }
        if (null !== $this->response) {
            throw new \RuntimeException('Response cannot be overwritten.');
        }
        $this->response = $response;
    }
    
    /**
     * @return null|ResponseInterface
     */
    public function getResponse()
    {
        return $this->response;
    }
    
    /**
     * @param RequestException $exception
     */
    public function setException(RequestException $exception)
    {
        if (null !== $this->response) {
            throw new \RuntimeException('Invalid state. Call has a response within.');
        }
        if (null !== $this->exception) {
            throw new \RuntimeException('Exception cannot be overwritten.');
        }
        $this->exception = $exception;
    }
    
    /**
     * @return RequestException
     */
    public function getException()
    {
        return $this->exception;
    }
    
    /**
     * @return bool
     */
    public function hasException()
    {
        return null !== $this->exception;
    }
    
    /**
     * @return bool
     */
    public function isSucceeded()
    {
        return null !== $this->getResponse() && $this->successCode === $this->getResponse()->getStatusCode();
    }
    
    /**
     * @return bool
     */
    public function isFinished()
    {
        return true === $this->hasException() || null !== $this->getResponse();
    }
}