<?php

namespace Getresponse\Sdk\Client\Operation;

use Getresponse\Sdk\Client\Exception\MalformedResponseDataException;
use Getresponse\Sdk\Client\Exception\RequestException;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * Class FailedOperationResponse
 * @package Getresponse\Sdk\Client\Operation
 */
class FailedOperationResponse implements OperationResponse
{
    /** @var ResponseInterface */
    private $response;

    /** @var RequestException */
    private $exception;
    
    /** @var RequestInterface | null */
    private $request;
    
    /**
     * OperationResponse constructor.
     */
    private function __construct()
    {
    }
    
    /**
     * @param ResponseInterface $response
     * @param RequestInterface $request
     * @return static
     */
    public static function createWithResponse(ResponseInterface $response, RequestInterface $request = null)
    {
        $newInstance = new static();
        $newInstance->response = $response;
        $newInstance->request = $request;
        return $newInstance;
    }
    
    /**
     * @param RequestException $exception
     * @param RequestInterface $request
     * @return static
     */
    public static function createWithException(RequestException $exception, RequestInterface $request = null)
    {
        $newInstance = new static();
        $newInstance->exception = $exception;
        $newInstance->request = $request;
        return $newInstance;
    }
    
    /**
     * @param RequestInterface $request
     * @return static
     */
    public static function createAsIncomplete(RequestInterface $request = null)
    {
        $newInstance = new static();
        $newInstance->request = $request;
        return $newInstance;
    }
    
    /**
     * @return bool
     */
    public function hasResponse()
    {
        return null !== $this->response;
    }
    
    /**
     * @return ResponseInterface
     */
    public function getResponse()
    {
        return $this->response;
    }
    
    /**
     * @return bool
     */
    public function isSuccess()
    {
        return false;
    }
    
    /**
     * @return string
     */
    public function getErrorMessage()
    {
        if ($this->exception) {
            return $this->exception->getMessage();
        }
        
        $body = $this->getData();
        return (isset($body['message']) ? $body['message'] : 'Operation failed');
    }
    
    /**
     * @return bool
     */
    public function hasException()
    {
        return null !== $this->exception;
    }
    
    /**
     * @return RequestException
     */
    public function getException()
    {
        return $this->exception;
    }
    
    /**
     * @return RequestInterface | null
     */
    public function getRequest()
    {
        return $this->request;
    }
    
    /**
     * @return boolean
     */
    public function hasRequest()
    {
        return null !== $this->request;
    }
    
    /**
     * @return array
     * @throws MalformedResponseDataException
     */
    public function getData()
    {
        return $this->hasResponse() ? $this->decodeJson((string) $this->response->getBody()) : [];
    }

    /**
     * @param string $json
     * @return array
     * @throws MalformedResponseDataException
     */
    private function decodeJson($json)
    {
        if ('' === $json) {
            return null;
        }
        $decodedData = json_decode($json, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw MalformedResponseDataException::createFromJsonLastErrorMsg($json);
        }

        return $decodedData;
    }
    
    /**
     * @return bool
     */
    public function isPaginated()
    {
        return false;
    }
    
    /**
     * @return RateLimit | null
     */
    public function getRateLimit()
    {
        if ($this->hasResponse()) {
            return new RateLimit(
                $this->response->getHeaderLine('x-ratelimit-limit'),
                $this->response->getHeaderLine('x-ratelimit-remaining'),
                $this->response->getHeaderLine('x-ratelimit-reset')
            );
        }
        return null;
    }

    /**
     * @return PaginationValues
     */
    public function getPaginationValues()
    {
        return new PaginationValues(null, null, null);
    }
}
