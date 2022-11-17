<?php
namespace Getresponse\Sdk\Client\Test\Unit\Debugger;

use Getresponse\Sdk\Client\Debugger\DataCollector;
use Getresponse\Sdk\Client\Debugger\DebugLogger;
use Getresponse\Sdk\Client\Handler\Call\CallInfo;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\Uri;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LogLevel;

/**
 * Class DebugLoggerTest
 * @package Getresponse\Sdk\Client\Test\Unit\Debugger
 */
class DebugLoggerTest extends TestCase
{
    /**
     * @var DebugLogger
     */
    private $systemUnderTest;
    
    /**
     * @var DataCollector|MockObject
     */
    private $dataCollectorMock;
    
    protected function setUp(): void
    {
        $this->dataCollectorMock = $this->createMock(DataCollector::class);
        $this->systemUnderTest = new DebugLogger($this->dataCollectorMock);
    }
    
    /**
     * @test
     */
    public function shouldAddRequestToDataCollector()
    {
        $request = new Request('GET', new Uri('http://domain.com'));
        
        $this->dataCollectorMock
            ->expects(static::once())
            ->method('collectRequest')
            ->withConsecutive([$this->equalTo($request)]);
        
        $this->systemUnderTest->debug('log message', [
            'request' => $request,
        ]);
    }
    
    /**
     * @test
     */
    public function shouldAddContextToDataCollector()
    {
        $request = new Request('GET', new Uri('http://domain.com'));
        $response = new Response(204);
        $info = new CallInfo();
        
        $this->dataCollectorMock
            ->expects(static::never())
            ->method('collectRequest');
        
        $this->dataCollectorMock
            ->expects(static::once())
            ->method('collectResponse')
            ->withConsecutive([$this->equalTo($response), $this->equalTo($request), $this->equalTo($info)]);
        
        $this->systemUnderTest->log(LogLevel::DEBUG, 'log message', [
            'request' => $request,
            'response' => $response,
            'info' => $info,
        ]);
    }
}
