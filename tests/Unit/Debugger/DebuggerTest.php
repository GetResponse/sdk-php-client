<?php
namespace Getresponse\Sdk\Client\Test\Unit\Debugger;

use Getresponse\Sdk\Client\Debugger\DataCollector;
use Getresponse\Sdk\Client\Debugger\DebugDumper;
use Getresponse\Sdk\Client\Debugger\Debugger;
use Getresponse\Sdk\Client\Debugger\Formatter;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Class DebuggerTest
 * @package Getresponse\Sdk\Client\Test\Unit\Debugger
 */
class DebuggerTest extends TestCase
{
    /**
     * @var Debugger
     */
    private $systemUnderTest;
    
    /**
     * @var DataCollector|MockObject
     */
    private $dataCollectorMock;
    
    protected function setUp(): void
    {
        $this->dataCollectorMock = $this->createMock(DataCollector::class);
        $this->systemUnderTest = new Debugger($this->dataCollectorMock);
    }
    
    /**
     * @test
     */
    public function shouldDebug()
    {
        $debugData = ['data' => []];
        $this->dataCollectorMock
            ->expects(static::once())
            ->method('getData')
            ->willReturn($debugData);
        
        $formattedDebugData = '{"data":[]}';
        $formatterMock = $this->createMock(Formatter::class);
        $formatterMock
            ->expects(static::once())
            ->method('format')
            ->withConsecutive([$this->equalTo($debugData)])
            ->willReturn($formattedDebugData);
        
        $debugDumperMock = $this->createMock(DebugDumper::class);
        $debugDumperMock
            ->expects(static::once())
            ->method('dump')
            ->withConsecutive([$this->equalTo($formattedDebugData)]);
        
        $this->systemUnderTest->debug($formatterMock, $debugDumperMock);
    }
}
