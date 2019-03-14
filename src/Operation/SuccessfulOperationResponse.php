<?php

namespace Getresponse\Sdk\Client\Operation;

use Getresponse\Sdk\Client\Exception\MalformedResponseDataException;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * Class SuccessfulOperationResponse
 * @package Getresponse\Sdk\Client\Operation
 */
class SuccessfulOperationResponse implements OperationResponse
{
    /**
     * @var ResponseInterface
     */
    private $response;

    /**
     * @var int
     */
    private $successCode;
    
    /**
     * @var RequestInterface | null
     */
    private $request;

    /**
     * OperationResponse constructor.
     * @param ResponseInterface $response
     * @param int $successCode
     * @param RequestInterface $request
     */
    public function __construct(ResponseInterface $response, $successCode, RequestInterface $request = null)
    {
        $this->response = $response;
        $this->successCode = (int) $successCode;
        $this->request = $request;
    }

    /**
     * @return bool
     */
    public function isSuccess()
    {
        return $this->successCode === $this->response->getStatusCode();
    }

    /**
     * @return array
     * @throws MalformedResponseDataException
     */
    public function getData()
    {
        return $this->decodeJson((string) $this->response->getBody());
    }

    /**
     * @param string $json
     * @return array
     * @throws MalformedResponseDataException
     */
    private function decodeJson($json)
    {
        $decodedData = json_decode($json, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw MalformedResponseDataException::createFromJsonLastErrorMsg($json);
        }

        return $decodedData;
    }

    /**
     * @return ResponseInterface
     */
    public function getResponse()
    {
        return $this->response;
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
     * @return bool
     */
    public function isPaginated()
    {
        return $this->response->hasHeader('totalcount')
            && $this->response->hasHeader('totalpages')
            && $this->response->hasHeader('currentpage');
    }

    /**
     * @return RateLimit
     */
    public function getRateLimit()
    {
        return new RateLimit(
            $this->response->getHeaderLine('x-ratelimit-limit'),
            $this->response->getHeaderLine('x-ratelimit-remaining'),
            $this->response->getHeaderLine('x-ratelimit-reset')
        );
    }

    public function getPaginationValues()
    {
        return new PaginationValues(
            (int)$this->response->getHeaderLine('CurrentPage'),
            (int)$this->response->getHeaderLine('TotalPages'),
            (int)$this->response->getHeaderLine('TotalCount')
        );
    }
}
