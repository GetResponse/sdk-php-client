<?php
namespace Getresponse\Sdk\Client\Test\Unit\Debugger;

use Getresponse\Sdk\Client\Debugger\DataCollector;
use Getresponse\Sdk\Client\Debugger\LineFormatter;
use Getresponse\Sdk\Client\Handler\Call\CallInfo;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\Uri;

/**
 * Class LineFormatterTest
 * @package Getresponse\Sdk\Client\Test\Unit\Debugger
 */
class LineFormatterTest extends \PHPUnit_Framework_TestCase
{
    /** @var LineFormatter */
    private $systemUnderTest;
    
    protected function setUp()
    {
        $this->systemUnderTest = new LineFormatter();
    }
    
    /**
     * @test
     */
    public function shouldFormatCallWithoutResponse()
    {
        $request = new Request(
            'POST',
            new Uri('https://api.getresponse.com/v3/contacts'),
            ['x-app-id' => 'one', 'x-auth-token' => 'api-key 637462874623846326473284'],
            '{"name":"My contact","email":"test@somedomain.com"}',
            '1.1'
        );
        
        $dataCollector = new DataCollector();
        $dataCollector->collectRequest($request);
        
        $output = $this->systemUnderTest->format($dataCollector->getData());
        static::assertEquals(sprintf(<<<EOT
--------- CALLS ---------
    1. POST: /v3/contacts
        URL: https://api.getresponse.com
        Authorization: api-key 637462874623846326473284
        Time: %s
        Protocol Version: 1.1
        Request Headers:
            host: api.getresponse.com
            x-app-id: one
            x-auth-token: api-key 637462874623846326473284
        Request Body:
        {
            "name": "My contact",
            "email": "test@somedomain.com"
        }
--------- SUMMARY ---------
    Date: %s
    Total time: 0 ms
    Calls: 1
    Errors count: 0
EOT
        . PHP_EOL .  $this->generateEnvBlock()
        , date('H:i:s'), date('r'))
        , $output);
    }
    
    /**
     * @test
     */
    public function shouldFormatCallWithEmptyResponseBody()
    {
        $request = new Request(
            'DELETE',
            new Uri('https://api.getresponse.com/v3/contacts'),
            ['x-app-id' => 'one', 'x-auth-token' => 'api-key 637462874623846326473284'],
            '',
            '1.1'
        );
    
        $responseHeaders = [
            'Content-Encoding' => 'gzip',
            'Content-Type' => 'application/json;charset=utf-8',
        ];
        $responseBody = '';
        $response = new Response(
            204,
            $responseHeaders,
            $responseBody
        );
    
        $info = (new CallInfo())
            ->withTotalTime(600)
            ->withRequestSize(120)
            ->withConnectTime(100)
            ->withSizeDownload(1024)
            ->withSpeedDownload(2048);
        
        $dataCollector = new DataCollector();
        $dataCollector->collectRequest($request);
        $dataCollector->collectResponse($response, $request, $info);
        
        $output = $this->systemUnderTest->format($dataCollector->getData());
        static::assertEquals(sprintf(<<<EOT
--------- CALLS ---------
    1. DELETE: /v3/contacts
        URL: https://api.getresponse.com
        Authorization: api-key 637462874623846326473284
        Time: %s
        Protocol Version: 1.1
        Request size: 120
        Request time: 500000 ms
        Connect time: 100000 ms
        Request Headers:
            host: api.getresponse.com
            x-app-id: one
            x-auth-token: api-key 637462874623846326473284
        Request Body:
            [EMPTY]
        Total time: 600000 ms
        Size download: 1024
        Speed download: 2048
        Result: 204 No Content
        Response Headers:
            Content-Encoding: gzip
            Content-Type: application/json;charset=utf-8
        Response Body:
            [EMPTY]
--------- SUMMARY ---------
    Date: %s
    Total time: 600000 ms
    Calls: 1
    Errors count: 0
EOT
        . PHP_EOL .  $this->generateEnvBlock()
        , date('H:i:s'), date('r'))
        , $output);
    }
    
    /**
     * @test
     */
    public function shouldFormatCallWithoutRequest()
    {
        $request = new Request(
            'POST',
            new Uri('https://api.getresponse.com/v3/contacts'),
            ['x-app-id' => 'one', 'x-auth-token' => 'api-key 637462874623846326473284'],
            '{"name":"My contact","email":"test@somedomain.com"}',
            '1.1'
        );
        
        $info = (new CallInfo())
            ->withTotalTime(600)
            ->withRequestSize(120)
            ->withConnectTime(100)
            ->withSizeDownload(1024)
            ->withSpeedDownload(2048);
    
        $responseHeaders = [
            'Content-Encoding' => 'gzip',
            'Content-Type' => 'application/json;charset=utf-8',
        ];
        $responseBody = '[{"contactId": "u2tzdP"}]';
        $response = new Response(
            201,
            $responseHeaders,
            $responseBody
        );
        
        $dataCollector = new DataCollector();
        $dataCollector->collectResponse($response, $request, $info);
        
        $output = $this->systemUnderTest->format($dataCollector->getData());
        static::assertEquals(sprintf(<<<EOT
--------- CALLS ---------
    1. POST: /v3/contacts
        URL: https://api.getresponse.com
        Authorization: api-key 637462874623846326473284
        Time: %s
        Protocol Version: 1.1
        Request size: 120
        Request time: 500000 ms
        Connect time: 100000 ms
        Request Headers:
            host: api.getresponse.com
            x-app-id: one
            x-auth-token: api-key 637462874623846326473284
        Request Body:
        {
            "name": "My contact",
            "email": "test@somedomain.com"
        }
        Total time: 600000 ms
        Size download: 1024
        Speed download: 2048
        Result: 201 Created
        Response Headers:
            Content-Encoding: gzip
            Content-Type: application/json;charset=utf-8
        Response Body:
        [
            {
                "contactId": "u2tzdP"
            }
        ]
--------- SUMMARY ---------
    Date: %s
    Total time: 600000 ms
    Calls: 1
    Errors count: 0
EOT
        . PHP_EOL .  $this->generateEnvBlock()
        , date('H:i:s'), date('r'))
        , $output);
    }
    
    /**
     * @test
     */
    public function shouldFormatCalls()
    {
        $requests[0] = new Request(
            'POST',
            new Uri('https://api.getresponse.com/v3/contacts'),
            ['x-app-id' => 'one', 'x-auth-token' => 'api-key 637462874623846326473284'],
            '{"name":"My contact","email":"test@somedomain.com"}',
            '1.1'
        );
        
        $requests[1] = new Request(
            'POST',
            new Uri('https://api.getresponse.com/v3/contacts'),
            ['x-app-id' => 'one', 'x-auth-token' => 'api-key 637462874623846326473284'],
            '{"name":"My contact","email":"test@somedomain.com"}',
            '1.1'
        );
        
        $info = (new CallInfo())
            ->withTotalTime(600)
            ->withRequestSize(120)
            ->withConnectTime(100)
            ->withSizeDownload(1024)
            ->withSpeedDownload(2048);
    
        $responseHeaders = [
            'Content-Encoding' => 'gzip',
            'Content-Type' => 'application/json;charset=utf-8',
        ];
        $responseBody = '[{"contactId": "u2tzdP"}]';
        $response = new Response(
            201,
            $responseHeaders,
            $responseBody
        );
        
        $dataCollector = new DataCollector();
        $dataCollector->collectRequest($requests[0]);
        $dataCollector->collectRequest($requests[1]);
        $dataCollector->collectResponse($response, $requests[0], $info);
        $dataCollector->collectResponse($response, $requests[1], $info);
        
        $output = $this->systemUnderTest->format($dataCollector->getData());
        static::assertEquals(sprintf(<<<EOT
--------- CALLS ---------
    1. POST: /v3/contacts
        URL: https://api.getresponse.com
        Authorization: api-key 637462874623846326473284
        Time: %s
        Protocol Version: 1.1
        Request size: 120
        Request time: 500000 ms
        Connect time: 100000 ms
        Request Headers:
            host: api.getresponse.com
            x-app-id: one
            x-auth-token: api-key 637462874623846326473284
        Request Body:
        {
            "name": "My contact",
            "email": "test@somedomain.com"
        }
        Total time: 600000 ms
        Size download: 1024
        Speed download: 2048
        Result: 201 Created
        Response Headers:
            Content-Encoding: gzip
            Content-Type: application/json;charset=utf-8
        Response Body:
        [
            {
                "contactId": "u2tzdP"
            }
        ]
    2. POST: /v3/contacts
        URL: https://api.getresponse.com
        Authorization: api-key 637462874623846326473284
        Time: %s
        Protocol Version: 1.1
        Request size: 120
        Request time: 500000 ms
        Connect time: 100000 ms
        Request Headers:
            host: api.getresponse.com
            x-app-id: one
            x-auth-token: api-key 637462874623846326473284
        Request Body:
        {
            "name": "My contact",
            "email": "test@somedomain.com"
        }
        Total time: 600000 ms
        Size download: 1024
        Speed download: 2048
        Result: 201 Created
        Response Headers:
            Content-Encoding: gzip
            Content-Type: application/json;charset=utf-8
        Response Body:
            [EMPTY]
--------- SUMMARY ---------
    Date: %s
    Total time: 1200000 ms
    Calls: 2
    Errors count: 0
EOT
        . PHP_EOL . $this->generateEnvBlock()
        , date('H:i:s'), date('H:i:s'), date('r'))
        , $output);
    }
    
    private function generateEnvBlock()
    {
        return sprintf(<<<EOT
--------- ENV ---------
    PHP Version: %s
    SAPI: %s
    xdebug: %s
    curl: %s
EOT
        ,
        phpversion(),
        php_sapi_name(),
        extension_loaded('xdebug') ? 'yes' : 'no',
        extension_loaded('curl') ? 'yes' : 'no'
        );
    }
}