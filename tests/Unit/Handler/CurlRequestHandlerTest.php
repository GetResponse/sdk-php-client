<?php

namespace Getresponse\Sdk\Client\Test\Unit\Handler;

use Getresponse\Sdk\Client\Debugger\Logger;
use Getresponse\Sdk\Client\Handler\Call\Call;
use Getresponse\Sdk\Client\Handler\Call\CallRegistry;
use Getresponse\Sdk\Client\Handler\CurlRequestHandler;
use Getresponse\Sdk\Client\Test\FunctionMock\FunctionMockRegistry;
use Getresponse\Sdk\Client\Test\FunctionMock\MockBuilder;
use GuzzleHttp\Psr7\Request;
use phpmock\functions\FixedValueFunction;
use Prophecy\Argument;
use Prophecy\Prophecy\ObjectProphecy;
use Psr\Http\Message\ResponseInterface;
use Psr\Log\LoggerInterface;

/**
 * Class CurlRequestHandlerTest
 * @package Unit\Handler
 */
class CurlRequestHandlerTest extends \PHPUnit_Framework_TestCase
{
    const HANDLER_NAMESPACE = 'Getresponse\Sdk\Client\Handler';
    
    /**
     * @var CurlRequestHandler
     */
    private $systemUnderTest;

    protected function setUp()
    {
        FunctionMockRegistry::resetAll();
        $this->systemUnderTest = new CurlRequestHandler();
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
    public function shouldSendGetRequest()
    {
        $request = new Request('GET', 'http://example.com', ['X-Test' => 'test-value']);
        
        $this->mockCurl(0);
    
        $call = new Call($request, 200);
        $this->systemUnderTest->send($call);
    
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

    /**
     * @test
     */
    public function shouldSendPostRequest()
    {
        $request = new Request('POST', 'http://example.com', ['X-Test' => 'test-value'], '{"a": "bcd"}');
        $call = new Call($request, 200);
        $this->mockCurl(0);

        $this->systemUnderTest->send($call);
        self::assertTrue($call->isSucceeded());
    }

    /**
     * @test
     */
    public function shouldSendDeleteRequest()
    {
        $request = new Request('DELETE', 'http://example.com', ['X-Test' => 'test-value']);
        $call = new Call($request, 200);
        $this->mockCurl(0);

        $this->systemUnderTest->send($call);
        self::assertTrue($call->isSucceeded());
    }

    /**
     * @test
     */
    public function shouldSendDeleteRequestWithBody()
    {
        $request = new Request('DELETE', 'http://example.com', ['X-Test' => 'test-value'], '{"a": "bcd"}');
        $call = new Call($request, 204);

        $this->mockCurl(0, '204 No content');

        $this->systemUnderTest->send($call);
        self::assertTrue($call->isSucceeded());
    }

    /**
     * @test
     * @expectedException \Getresponse\Sdk\Client\Exception\ConnectException
     */
    public function shouldThrowConnectExceptionOnCurlError()
    {
        $request = new Request('GET', 'http://example.com', ['X-Test' => 'test-value']);
        $call = new Call($request, 200);
        $this->mockCurl(1);

        $this->systemUnderTest->send($call);
        self::assertFalse($call->isSucceeded());
        self::assertTrue($call->hasException());
        throw $call->getException();
    }

    /**
     * @test
     * @expectedException \Getresponse\Sdk\Client\Exception\ConnectException
     */
    public function shouldThrowConnectExceptionOnParseMessageException()
    {
        $request = new Request('GET', 'http://example.com');
        $call = new Call($request, 200);
        $this->mockCurl(0, '2000 NOT OK');

        $this->systemUnderTest->send($call);
        self::assertFalse($call->isSucceeded());
        self::assertTrue($call->hasException());
        throw $call->getException();
    }
    
    /**
     * @test
     */
    public function shouldSendManyRequests()
    {
        $callRegistry = new CallRegistry();
        $callRegistry->registerRequest(new Request('GET', 'http://example.com', ['X-Test' => 'test-value']), 200);
        $callRegistry->registerRequest(new Request('GET', 'http://example.com/test', ['X-Test2' => 'test2-value2']), 200);
        
        $this->mockCurl(0);
        
        $this->systemUnderTest->sendMany($callRegistry);
        
        /** @var ResponseInterface $response */
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
     */
    public function shouldLogRequestAndResponse()
    {
        /** @var LoggerInterface | ObjectProphecy $loggerMock */
        $loggerMock = $this->prophesize(LoggerInterface::class);

        $systemUnderTest = new CurlRequestHandler();
        $systemUnderTest->setLogger(new Logger($loggerMock->reveal()));

        $this->mockCurl(0);

        $request = new Request('GET', 'http://example.com');
        $call = new Call($request, 200);

        $loggerMock->debug('Sending: GET http://example.com', ['request' => $request])->shouldBeCalled()->willReturn(null);
        $loggerMock->debug('Received: 200', Argument::withKey('response'))->shouldBeCalled()->willReturn(null);

        $systemUnderTest->send($call);
    }
    
    /**
     * @test
     */
    public function shouldLogRequestAndException()
    {
        /** @var LoggerInterface | ObjectProphecy $loggerMock */
        $loggerMock = $this->prophesize(LoggerInterface::class);

        $systemUnderTest = new CurlRequestHandler();
        $systemUnderTest->setLogger(new Logger($loggerMock->reveal()));

        $this->mockCurl(1);

        $request = new Request('GET', 'http://example.com');
        $call = new Call($request, 200);

        $loggerMock->debug('Sending: GET http://example.com', ['request' => $request])->shouldBeCalled()->willReturn(null);
        $loggerMock->debug(Argument::containingString('Thrown exception'), Argument::withKey('request'))->shouldBeCalled()->willReturn(null);

        $systemUnderTest->send($call);
    }

    /**
     * @param int $errno
     * @param string $responseCode
     * @throws \phpmock\MockEnabledException
     */
    private function mockCurl($errno, $responseCode = '200 OK')
    {
        $builder = new MockBuilder();
        $builder
            ->setNamespace(self::HANDLER_NAMESPACE)
            ->setName('curl_init')
            ->setFunctionProvider(new FixedValueFunction('res#1'))
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
                if ($opt === CURLOPT_POSTFIELDS) {
                    self::assertEquals('{"a": "bcd"}', $value);
                }
                return null;
            })
            ->build();

        $builder
            ->setNamespace(self::HANDLER_NAMESPACE)
            ->setName('curl_errno')
            ->setFunctionProvider(new FixedValueFunction($errno))
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
            ->setName('curl_close')
            ->setFunctionProvider(new FixedValueFunction(null))
            ->build();

        $builder
            ->setNamespace(self::HANDLER_NAMESPACE)
            ->setName('curl_exec')
            ->setFunctionProvider(new FixedValueFunction(
                'HTTP/1.1 ' . $responseCode . '
Connection: keep-alive
Server: gunicorn/19.7.1
Date: Wed, 12 Apr 2017 09:13:11 GMT
Content-Type: application/json
Access-Control-Allow-Origin: *
Access-Control-Allow-Credentials: true
Content-Length: 377
Via: 1.1 vegur

{
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
            ))
            ->build();

        $builder
            ->setNamespace('Getresponse\Sdk\Client\Exception')
            ->setName('curl_getinfo')
            ->setFunctionProvider(new FixedValueFunction([]))
            ->build();
        
        $builder
            ->setNamespace(self::HANDLER_NAMESPACE)
            ->setName('curl_getinfo')
            ->setFunctionProvider(new FixedValueFunction([
                'connect_time' => 100,
                'request_size' => 128,
                'size_download' => 2450,
                'speed_download' => 7000,
                'total_time' => 142,
            ]))
            ->build();
    }
}
