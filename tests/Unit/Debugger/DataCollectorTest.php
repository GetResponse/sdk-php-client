<?php
namespace Getresponse\Sdk\Client\Test\Unit\Debugger;

use Getresponse\Sdk\Client\Debugger\DataCollector;
use Getresponse\Sdk\Client\Handler\Call\CallInfo;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\Uri;

/**
 * Class DataCollectorTest
 * @package Getresponse\Sdk\Client\Test\Unit\Debugger
 */
class DataCollectorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var DataCollector
     */
    private $systemUnderTest;
    
    protected function setUp()
    {
        $this->systemUnderTest = new DataCollector();
    }
    
    /**
     * @test
     */
    public function shouldCollectRequestData()
    {
        $requestHeaders = ['x-auth-token' => 'api-key 77777777777'];
        $requestBody = '{"name":"new contact","email":"some.name@domain.com"}';
        
        $request = new Request(
            'POST',
            new Uri('https://api.getresponse.com/v3/contacts'),
            $requestHeaders,
            $requestBody,
            '2.0'
        );
        
        $this->systemUnderTest->collectRequest($request);
        $data = $this->systemUnderTest->getData();
        $this->assertMainData($data);
        
        foreach ($data['calls'] as $call) {
            static::assertArrayHasKey('datetime', $call);
    
            $this->assertRequest($call, $request, $requestHeaders, $requestBody);
            
            static::assertArrayNotHasKey('response', $call);
            static::assertArrayNotHasKey('metrics', $call);
        }
    }
    
    /**
     * @test
     */
    public function shouldCollectRequestDataWithInvalidPayload()
    {
        $requestHeaders = ['x-auth-token' => 'api-key 77777777777'];
        $requestBody = '{"name":"new contact","email":"some.name@domain.com"'; // syntax error
        
        $request = new Request(
            'POST',
            new Uri('https://api.getresponse.com/v3/contacts'),
            $requestHeaders,
            $requestBody,
            '2.0'
        );
        
        $this->systemUnderTest->collectRequest($request);
        $data = $this->systemUnderTest->getData();
        $this->assertMainData($data);
        
        foreach ($data['calls'] as $call) {
            static::assertArrayHasKey('datetime', $call);
    
            $this->assertRequest($call, $request, $requestHeaders, '');
            static::assertArrayHasKey('parse_errors', $call['request']);
            
            static::assertArrayNotHasKey('response', $call);
            static::assertArrayNotHasKey('metrics', $call);
        }
    }
    
    /**
     * @test
     */
    public function shouldCollectResponseData()
    {
        $requestHeaders = ['x-auth-token' => 'api-key 77777777777'];
        $requestBody = '{"name":"new contact","email":"some.name@domain.com"}';
        
        $request = new Request(
            'POST',
            new Uri('https://api.getresponse.com/v3/contacts'),
            $requestHeaders,
            $requestBody,
            '2.0'
        );
        
        $this->systemUnderTest->collectRequest($request);
    
        $info = (new CallInfo())
            ->withTotalTime(600)
            ->withRequestSize(128)
            ->withConnectTime(100)
            ->withSizeDownload(1024)
            ->withSpeedDownload(2048);
        
        $responseHeaders = [
            'Content-Encoding' => 'gzip',
            'Content-Type' => 'application/json;charset=utf-8',
            'Date' => 'Tue, 20 Jun 2017 06:48:53 GMT',
            'Server' => 'nginx',
            'Transfer-Encoding' => 'chunked',
        ];
        $responseBody = '[{
            "contactId": "u2tzdP",
            "href": "https://api.getresponse.com/v3/contacts/u2tzdP",
            "name": null,
            "email": "michal+split-test41@giergielewicz.pl",
            "note": null,
            "origin": "api",
            "dayOfCycle": null,
            "changedOn": "2017-03-02T07:47:58+0000",
            "timeZone": "",
            "ipAddress": "",
            "activities": "https://api.getresponse.com/v3/contacts/u2tzdP/activities",
            "campaign": {
              "campaignId": "TyD1a",
              "href": "https://api.getresponse.com/v3/campaigns/TyD1a",
              "name": "sprint_bravo"
            },
            "createdOn": "2017-03-02T07:47:58+0000",
            "scoring": null
        }]';
        $response = new Response(
            201,
            $responseHeaders,
            $responseBody
        );
        
        $this->systemUnderTest->collectResponse($response, $request, $info);
        $data = $this->systemUnderTest->getData();
        $this->assertMainData($data);
        
        foreach ($data['calls'] as $call) {
            static::assertArrayHasKey('datetime', $call);
    
            $this->assertRequest($call, $request, $requestHeaders, $requestBody);
            
            static::assertArrayHasKey('response', $call);
            static::assertEquals($response->getStatusCode(), $call['response']['statusCode']);
            static::assertEquals($response->getReasonPhrase(), $call['response']['reasonPhrase']);
            static::assertEquals($responseHeaders, $call['response']['headers']);
            static::assertEquals(json_decode($responseBody, true), $call['response']['body']);
    
            $this->assertCallMetrics($call);
        }
    }
    
    /**
     * @test
     */
    public function shouldCollectAsyncResponseData()
    {
        $requestHeaders = ['x-auth-token' => 'api-key 77777777777'];
        $requestBody = '{"name":"new contact","email":"some.name@domain.com"}';
        
        $request = new Request(
            'POST',
            new Uri('https://api.getresponse.com/v3/contacts'),
            $requestHeaders,
            $requestBody,
            '2.0'
        );
    
        $info = (new CallInfo())
            ->withTotalTime(600)
            ->withRequestSize(128)
            ->withConnectTime(100)
            ->withSizeDownload(1024)
            ->withSpeedDownload(2048);
        
        $responseHeaders = [
            'Content-Type' => 'application/json;charset=utf-8',
            'Date' => 'Tue, 20 Jun 2017 06:48:53 GMT',
            'Transfer-Encoding' => 'chunked',
        ];
        $responseBody = '{
            "httpStatus": 401,
            "code": 1014,
            "codeDescription": "Problem during authentication process, check headers!",
            "message": "Unable to authenticate request. Check credentials or authentication method details",
            "moreInfo": "https://apidocs.getresponse.com/en/v3/errors/1014",
            "context": {
                "authenticationType": "auth_token"
            },
            "uuid": "474a4b57-df76-4dd9-be3e-31625d93cae6"
        }';
        $response = new Response(
            401,
            $responseHeaders,
            $responseBody
        );
        
        $this->systemUnderTest->collectResponse($response, $request, $info);
        $data = $this->systemUnderTest->getData();
        $this->assertMainData($data);
        
        foreach ($data['calls'] as $call) {
            static::assertArrayHasKey('datetime', $call);
            $this->assertRequest($call, $request, $requestHeaders, $requestBody);
    
            static::assertArrayHasKey('response', $call);
            static::assertEquals($response->getStatusCode(), $call['response']['statusCode']);
            static::assertEquals($response->getReasonPhrase(), $call['response']['reasonPhrase']);
            static::assertEquals($responseHeaders, $call['response']['headers']);
            static::assertEquals(json_decode($responseBody, true), $call['response']['body']);
    
            $this->assertCallMetrics($call);
        }
    }
    
    /**
     * @test
     */
    public function shouldCollectResponseDataWithInvalidBody()
    {
        $requestHeaders = ['x-auth-token' => 'api-key 77777777777'];
        $requestBody = '{"name":"new contact","email":"some.name@domain.com"}';
        
        $request = new Request(
            'POST',
            new Uri('https://api.getresponse.com/v3/contacts'),
            $requestHeaders,
            $requestBody,
            '2.0'
        );
        
        $this->systemUnderTest->collectRequest($request);
    
        $info = (new CallInfo())
            ->withTotalTime(600)
            ->withRequestSize(128)
            ->withConnectTime(100)
            ->withSizeDownload(1024)
            ->withSpeedDownload(2048);
        
        $responseHeaders = [
            'Content-Encoding' => 'gzip',
            'Content-Type' => 'application/json;charset=utf-8',
            'Date' => 'Tue, 20 Jun 2017 06:48:53 GMT',
            'Server' => 'nginx',
            'Transfer-Encoding' => 'chunked',
        ];
        // syntax error
        $responseBody = '[{
            "contactId": "u2tzdP"
            "href": "https://api.getresponse.com/v3/contacts/u2tzdP",
            "name": null,
            "email": "michal+split-test41@giergielewicz.pl",
            "note": null,
            "origin": "api",
            "dayOfCycle": null,
            "changedOn": "2017-03-02T07:47:58+0000",
            "timeZone": "",
            "ipAddress": "",
            "activities": "https://api.getresponse.com/v3/contacts/u2tzdP/activities",
            "campaign": {
              "campaignId": "TyD1a",
              "href": "https://api.getresponse.com/v3/campaigns/TyD1a",
              "name": "sprint_bravo"
            },
            "createdOn": "2017-03-02T07:47:58+0000",
            "scoring": null
        }]';
        $response = new Response(
            201,
            $responseHeaders,
            $responseBody
        );
        
        $this->systemUnderTest->collectResponse($response, $request, $info);
        $data = $this->systemUnderTest->getData();
        $this->assertMainData($data);
        
        foreach ($data['calls'] as $call) {
            static::assertArrayHasKey('datetime', $call);
    
            $this->assertRequest($call, $request, $requestHeaders, $requestBody);
            
            static::assertArrayHasKey('response', $call);
            static::assertEquals($response->getStatusCode(), $call['response']['statusCode']);
            static::assertEquals($response->getReasonPhrase(), $call['response']['reasonPhrase']);
            static::assertEquals($responseHeaders, $call['response']['headers']);
            static::assertEquals('', $call['response']['body']);
            static::assertArrayHasKey('parse_errors', $call['response']);
    
            $this->assertCallMetrics($call);
        }
    }
    
    /**
     * @test
     */
    public function shouldCollectMultipleRequestsData()
    {
        $requestHeaders = ['x-auth-token' => 'api-key 77777777777'];
        $requestBody = '{"name":"new contact","email":"some.name@domain.com"}';
        
        $requests[0] = new Request(
            'POST',
            new Uri('https://api.getresponse.com/v3/contacts'),
            $requestHeaders,
            $requestBody,
            '2.0'
        );
    
        $requests[1] = new Request(
            'POST',
            new Uri('https://api.getresponse.com/v3/contacts'),
            $requestHeaders,
            $requestBody,
            '2.0'
        );
        
        $this->systemUnderTest->collectRequest($requests[0]);
        $this->systemUnderTest->collectRequest($requests[1]);
    
        $data = $this->systemUnderTest->getData();
        $this->assertMainData($data);
        static::assertEquals(count($requests), count($data['metrics']['operations']));
        static::assertEquals(count($requests), count($data['calls']));
        
        $index = 0;
        foreach ($data['calls'] as $call) {
            static::assertArrayHasKey('datetime', $call);
    
            $this->assertRequest($call, $requests[$index], $requestHeaders, $requestBody);
    
            static::assertArrayNotHasKey('response', $call);
            static::assertArrayNotHasKey('metrics', $call);
        }
    }
    
    /**
     * @param array $data
     */
    private function assertMainData(array $data)
    {
        static::assertArrayHasKey('date', $data);
        static::assertArrayHasKey('php_version', $data);
        static::assertArrayHasKey('sapi', $data);
        static::assertArrayHasKey('xdebug', $data);
        static::assertArrayHasKey('curl', $data);
        
        static::assertArrayHasKey('metrics', $data);
        static::assertArrayHasKey('calls', $data['metrics']);
        static::assertArrayHasKey('operations', $data['metrics']);
        static::assertNotEmpty($data['metrics']['operations']);
        static::assertArrayHasKey('error_count', $data['metrics']);
        static::assertArrayHasKey('total_time', $data['metrics']);
    
        static::assertArrayHasKey('calls', $data);
    }
    
    /**
     * @param array $call
     */
    private function assertCallMetrics(array $call)
    {
        static::assertArrayHasKey('metrics', $call);
        static::assertArrayHasKey('request_size', $call['metrics']);
        static::assertArrayHasKey('connect_time', $call['metrics']);
        static::assertArrayHasKey('request_time', $call['metrics']);
        static::assertArrayHasKey('total_time', $call['metrics']);
        static::assertArrayHasKey('size_download', $call['metrics']);
        static::assertArrayHasKey('speed_download', $call['metrics']);
    }
    
    /**
     * @param array $call
     * @param Request $request
     * @param array $requestHeaders
     * @param string $requestBody
     */
    private function assertRequest(array $call, Request $request, array $requestHeaders, $requestBody)
    {
        static::assertArrayHasKey('request', $call);
        static::assertEquals($request->getMethod(), $call['request']['method']);
        static::assertEquals(
            $request->getUri()->getScheme() . '://' . $request->getUri()->getAuthority(),
            $call['request']['url']
        );
        static::assertEquals(urldecode($request->getRequestTarget()), $call['request']['path']);
        $authorization = !empty($requestHeaders['authorization'])
            ? $requestHeaders['authorization']
            : $requestHeaders['x-auth-token'];
        static::assertEquals($authorization, $call['request']['authorization']);
        static::assertEquals($request->getProtocolVersion(), $call['request']['protocolVersion']);
        static::assertEquals($requestHeaders['x-auth-token'], $call['request']['headers']['x-auth-token']);
        if ('' !== $requestBody) {
            static::assertEquals(json_decode($requestBody, true), $call['request']['body']);
        } else {
            static::assertEmpty($call['request']['body']);
        }
    }
}