<?php

namespace Getresponse\Sdk\Client\Operation;

use Getresponse\Sdk\Client\Exception\MalformedResponseDataException;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * Class OperationResponse
 * @package Getresponse\Sdk\Client\Operation
 */
interface OperationResponse
{
    /**
     * @return bool
     */
    public function isSuccess();

    /**
     * @return array
     * @throws MalformedResponseDataException
     */
    public function getData();

    /**
     * @return ResponseInterface
     */
    public function getResponse();
    
    /**
     * @return RequestInterface | null
     */
    public function getRequest();
    
    /**
     * @return boolean
     */
    public function hasRequest();
    
    /**
     * @return bool
     */
    public function isPaginated();

    /**
     * @return RateLimit
     */
    public function getRateLimit();

    /**
     * @return PaginationValues
     */
    public function getPaginationValues();
}
