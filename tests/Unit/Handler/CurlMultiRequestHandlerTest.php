<?php

namespace Getresponse\Sdk\Client\Test\Unit\Handler;

use Getresponse\Sdk\Client\Exception\ConnectException;
use Getresponse\Sdk\Client\Handler\Call\CallRegistry;
use Getresponse\Sdk\Client\Handler\CurlMultiRequestHandler;
use Getresponse\Sdk\Client\Handler\CurlRequestHandler;
use Getresponse\Sdk\Client\Test\FunctionMock\FunctionMockRegistry;
use Getresponse\Sdk\Client\Test\FunctionMock\MockBuilder;
use GuzzleHttp\Psr7\Request;
use phpmock\functions\FixedValueFunction;

/**
 * Class CurlMultiRequestHandlerTest
 * @package Unit\Handler
 */
class CurlMultiRequestHandlerTest extends \PHPUnit_Framework_TestCase
{
    const HANDLER_NAMESPACE = 'Getresponse\Sdk\Client\Handler';

    /**
     * @var CurlMultiRequestHandler
     */
    private $systemUnderTest;

    protected function setUp()
    {
        FunctionMockRegistry::resetAll();
        $this->systemUnderTest = new CurlMultiRequestHandler();
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
    public function shouldExtendCurlRequestHandler()
    {
        self::assertInstanceOf(CurlRequestHandler::class, $this->systemUnderTest);
    }

    /**
     * @test
     */
    public function shouldSendManyRequests()
    {
        $callRegistry = new CallRegistry();
        $callRegistry->registerRequest(new Request('GET', 'http://example.com', ['X-Test' => 'test-value']), 200);
        $callRegistry->registerRequest(new Request('GET', 'http://example.com/test', ['X-Test2' => 'test2-value2']), 200);
        
        $this->mockCurl(
            0,
            '200 OK',
            '{
              "args": {},
              "data": "",
              "files": {},
              "form": {
                "abcdef": ""
              },
              "headers": {
                "Accept": "*/*",
                "Connection": "close",
                "Content-Length": "6",
                "Content-Type": "application/x-www-form-urlencoded",
                "Host": "httpbin.org",
                "X-Test": "x-test"
              },
              "json": null,
              "origin": "178.16.117.241",
              "url": "https://httpbin.org/post"
            }'
        );

        $this->systemUnderTest->sendMany($callRegistry);

        foreach ($callRegistry as $call) {
            $response = $call->getResponse();
            self::assertEquals(200, $response->getStatusCode());
            self::assertEquals(['keep-alive'], $response->getHeader('connection'));
            self::assertEquals(['application/json'], $response->getHeader('content-type'));
            self::assertEquals(['377'], $response->getHeader('content-length'));
            self::assertEquals(
                [
                    'args' => [],
                    'data' => '',
                    'files' => [],
                    'form' => ['abcdef' => ''],
                    'headers' => [
                        'Accept' => '*/*',
                        'Connection' => 'close',
                        'Content-Length' => '6',
                        'Content-Type' => 'application/x-www-form-urlencoded',
                        'Host' => 'httpbin.org',
                        'X-Test' => 'x-test'
                    ],
                    'json' => null,
                    'origin' => '178.16.117.241',
                    'url' => 'https://httpbin.org/post'
                ],
                json_decode($response->getBody(), true)
            );
        }
    }
    
    /**
     * @test
     * @expectedException \Getresponse\Sdk\Client\Exception\CallLimitOutOfBoundsException
     */
    public function shouldThrowCallLimitOutOfBoundsExceptionWhenRegisterTooManyCalls()
    {
        $callRegistry = new CallRegistry();
        for ($c = 0; $c <= CurlMultiRequestHandler::MAX_CALLS_LIMIT; $c++) {
            $callRegistry->registerRequest(new Request('GET', 'http://example.com', ['X-Test' => 'test-value']), 200);
        }
        $this->systemUnderTest->sendMany($callRegistry);
    }
    
    /**
     * @test
     */
    public function shouldThrowConnectExceptionOnCurlError()
    {
        $callRegistry = new CallRegistry();
        $callRegistry->registerRequest(new Request('GET', 'http://example.com', ['X-Test' => 'test-value']), 200);
        $callRegistry->registerRequest(new Request('GET', 'http://example.com/test', ['X-Test2' => 'test2-value2']), 200);

        $this->mockCurl(1);

        $this->systemUnderTest->sendMany($callRegistry);
        foreach ($callRegistry as $call) {
            self::assertTrue($call->hasException());
            self::assertInstanceOf(ConnectException::class, $call->getException());
        }
    }

    /**
     * @param int $errno
     * @param string $responseCode
     * @param string $responseBody
     * @throws \phpmock\MockEnabledException
     */
    private function mockCurl($errno, $responseCode = '200 OK', $responseBody = '')
    {
        $builder = new MockBuilder();
        $builder
            ->setNamespace(self::HANDLER_NAMESPACE)
            ->setName('curl_init')
            ->setFunctionProvider(new FixedValueFunction('res#1'))
            ->build();

        $builder
            ->setNamespace(self::HANDLER_NAMESPACE)
            ->setName('curl_multi_init')
            ->setFunctionProvider(new FixedValueFunction('multi_res#1'))
            ->build();

        $builder
            ->setNamespace(self::HANDLER_NAMESPACE)
            ->setName('curl_errno')
            ->setFunctionProvider(new FixedValueFunction($errno))
            ->build();

        $builder
            ->setNamespace(self::HANDLER_NAMESPACE)
            ->setName('curl_setopt')
            ->setFunction(function ($handle, $opt, $value) {
                self::assertContains($opt, [
                    CURLOPT_URL,
                    CURLOPT_USERAGENT,
                    CURLOPT_RETURNTRANSFER,
                    CURLOPT_HEADER,
                    CURLOPT_POST,
                    CURLOPT_POSTFIELDS,
                    CURLOPT_CUSTOMREQUEST,
                    CURLOPT_HTTPHEADER
                ]);
                return null;
            })
            ->build();

        $builder
            ->setNamespace(self::HANDLER_NAMESPACE)
            ->setName('curl_multi_add_handle')
            ->setFunction(function ($multiHandle, $curlHandle) {
                self::assertEquals('multi_res#1', $multiHandle);
                self::assertEquals('res#1', $curlHandle);
            })
            ->build();

        $builder
            ->setNamespace(self::HANDLER_NAMESPACE)
            ->setName('curl_multi_exec')
            ->setFunction(function ($multiHandle, $isRunning) {
                $isRunning = false;
            })
            ->build();

        $builder
            ->setNamespace(self::HANDLER_NAMESPACE)
            ->setName('curl_error')
            ->setFunctionProvider(new FixedValueFunction('cURL error message'))
            ->build();

        $builder
            ->setNamespace('Getresponse\Sdk\Client\Exception')
            ->setName('curl_error')
            ->setFunctionProvider(new FixedValueFunction('cURL error message'))
            ->build();

        $builder
            ->setNamespace(self::HANDLER_NAMESPACE)
            ->setName('curl_multi_getcontent')
            ->setFunction(function ($handle) use ($responseCode, $responseBody) {
                self::assertEquals('res#1', $handle);

                return 'HTTP/1.1 ' . $responseCode . '
Connection: keep-alive
Server: gunicorn/19.7.1
Date: Wed, 12 Apr 2017 09:13:11 GMT
Content-Type: application/json
Access-Control-Allow-Origin: *
Access-Control-Allow-Credentials: true
Content-Length: 377
Via: 1.1 vegur

' . $responseBody;
            })
            ->build();

        $builder
            ->setNamespace(self::HANDLER_NAMESPACE)
            ->setName('curl_close')
            ->setFunctionProvider(new FixedValueFunction(null))
            ->build();

        $builder
            ->setNamespace(self::HANDLER_NAMESPACE)
            ->setName('curl_multi_remove_handle')
            ->setFunction(function ($multiHandle, $handle) {
                self::assertEquals('multi_res#1', $multiHandle);
                self::assertEquals('res#1', $handle);
            })
            ->build();

        $builder
            ->setNamespace(self::HANDLER_NAMESPACE)
            ->setName('curl_multi_close')
            ->setFunction(function ($multiHandle) {
                self::assertEquals('multi_res#1', $multiHandle);
            })
            ->build();

        $builder
            ->setNamespace('Getresponse\Sdk\Client\Exception')
            ->setName('curl_getinfo')
            ->setFunctionProvider(new FixedValueFunction(false))
            ->build();

        $builder
            ->setNamespace(self::HANDLER_NAMESPACE)
            ->setName('curl_getinfo')
            ->setFunctionProvider(new FixedValueFunction(false))
            ->build();
    }
}
