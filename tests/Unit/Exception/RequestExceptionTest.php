<?php

namespace Getresponse\Sdk\Client\Test\Unit\Exception;

use Getresponse\Sdk\Client\Exception\RequestException;
use Getresponse\Sdk\Client\Test\FunctionMock\FunctionMockRegistry;
use Getresponse\Sdk\Client\Test\FunctionMock\MockBuilder;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;


/**
 * Class RequestExceptionTest
 * @package Getresponse\Sdk\Client\Test\Unit\Exception
 */
class RequestExceptionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @inheritDoc
     */
    protected function setUp()
    {
        FunctionMockRegistry::resetAll();
    }
    
    /**
     * @inheritDoc
     */
    protected function tearDown()
    {
        FunctionMockRegistry::resetAll();
    }
    
    /**
     * @test
     */
    public function shouldBeAbleToThrow()
    {
        try {
            $request = new Request('get', 'http://url.com', []);
            $response = new Response(500, []);
            $handlerInfo = [
                'curl' => 'info'
            ];
            throw new RequestException(
                'error message',
                $request,
                $handlerInfo,
                'clientVersion',
                $response
            );
        } catch (RequestException $e) {
            static::assertEquals($request, $e->getRequest());
            static::assertEquals($response, $e->getResponse());
            static::assertEquals($handlerInfo, $e->getHandlerInfo());
            static::assertEquals('clientVersion', $e->getClientVersion());
        }
    }

    /**
     * @test
     */
    public function shouldGetInfoFromCurlHandle()
    {
        $curlInfo = ['foo' => 'bar'];
        $curlError = 'failed to connect';

        $builder = new MockBuilder();
        $builder->setNamespace('Getresponse\Sdk\Client\Exception')
                ->setName('curl_getinfo')
                ->setFunction(
                    function () use($curlInfo){
                        return $curlInfo;
                    }
                )
                ->build();

        $builder->setNamespace('Getresponse\Sdk\Client\Exception')
                ->setName('curl_error')
                ->setFunction(
                    function () use($curlError) {
                        return $curlError;
                    }
                )
                ->build();

        $handlerInfo = RequestException::getHandlerInfoFromCurlHandler(curl_init());

        static::assertEquals($curlInfo, $handlerInfo['info']);
        static::assertEquals($curlError, $handlerInfo['error']);
    }

    /**
     * @test
     */
    public function shouldReturnResponseBodyIfResponseIsSet()
    {
        $request = new Request('get', 'http://url.com', []);
        $response = new Response(500, [], 'error response: internal server error');
        $handlerInfo = [
            'curl' => 'info'
        ];
        $systemUnderTest = new RequestException(
            'error message',
            $request,
            $handlerInfo,
            'clientVersion',
            $response
        );

        static::assertEquals('error response: internal server error', $systemUnderTest->getResponseBody());
    }

    /**
     * @test
     */
    public function shouldReturnEmptyStringAsResponseBodyIfResponseIsNotSet()
    {
        $request = new Request('get', 'http://url.com', []);
        $handlerInfo = [
            'curl' => 'info'
        ];
        $systemUnderTest = new RequestException(
            'error message',
            $request,
            $handlerInfo,
            'clientVersion'
        );

        static::assertEquals('', $systemUnderTest->getResponseBody());
    }
}
