<?php

namespace Getresponse\Sdk\Client\Test\Unit\Operation;

use Getresponse\Sdk\Client\Exception\RequestException;
use Getresponse\Sdk\Client\Handler\Call\Call;
use Getresponse\Sdk\Client\Operation\FailedOperationResponse;
use Getresponse\Sdk\Client\Operation\OperationResponseFactory;
use Getresponse\Sdk\Client\Operation\SuccessfulOperationResponse;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\RequestInterface;

/**
 * Class OperationResponseFactoryTest
 * @package Getresponse\Sdk\Client\Test\Unit\Operation
 */
class OperationResponseFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function shouldCreateSucceededByCallWithResponse()
    {
        $call = new Call(new Request('GET', 'https://api.getresponse.com'), 200);
        $call->setResponse(new Response(200));
        $operationResponse = OperationResponseFactory::createByCall($call);
        self::assertInstanceOf(SuccessfulOperationResponse::class, $operationResponse);
        self::assertTrue($operationResponse->hasRequest());
        self::assertInstanceOf(RequestInterface::class, $operationResponse->getRequest());
    }
    
    /**
     * @test
     */
    public function shouldCreateFailedByCallWithOtherSuccessCode()
    {
        $call = new Call(new Request('GET', 'https://api.getresponse.com'), 200);
        $call->setResponse(new Response(204));
        $operationResponse = OperationResponseFactory::createByCall($call);
        self::assertInstanceOf(FailedOperationResponse::class, $operationResponse);
        self::assertTrue($operationResponse->hasRequest());
        self::assertInstanceOf(RequestInterface::class, $operationResponse->getRequest());
    }
    
    /**
     * @test
     */
    public function shouldCreateFailedByCallWithException()
    {
        $call = new Call(new Request('GET', 'https://api.getresponse.com'), 200);
        $call->setException($this->prophesize(RequestException::class)->reveal());
        $operationResponse = OperationResponseFactory::createByCall($call);
        self::assertInstanceOf(FailedOperationResponse::class, $operationResponse);
        self::assertTrue($operationResponse->hasRequest());
        self::assertInstanceOf(RequestInterface::class, $operationResponse->getRequest());
    }
    
    /**
     * @test
     */
    public function shouldCreateFailedByNotFinishedCall()
    {
        $call = new Call(new Request('GET', 'https://api.getresponse.com'), 200);
        $operationResponse = OperationResponseFactory::createByCall($call);
        self::assertInstanceOf(FailedOperationResponse::class, $operationResponse);
        self::assertTrue($operationResponse->hasRequest());
        self::assertInstanceOf(RequestInterface::class, $operationResponse->getRequest());
    }
}
