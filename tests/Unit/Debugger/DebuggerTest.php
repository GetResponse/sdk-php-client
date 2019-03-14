<?php
namespace Getresponse\Sdk\Client\Test\Unit\Debugger;

use Getresponse\Sdk\Client\Debugger\DataCollector;
use Getresponse\Sdk\Client\Debugger\DebugDumper;
use Getresponse\Sdk\Client\Debugger\Debugger;
use Getresponse\Sdk\Client\Debugger\Formatter;

/**
 * Class DebuggerTest
 * @package Getresponse\Sdk\Client\Test\Unit\Debugger
 */
class DebuggerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Debugger
     */
    private $systemUnderTest;
    
    /**
     * @var DataCollector | \PHPUnit_Framework_MockObject_MockObject
     */
    private $dataCollectorMock;
    
    protected function setUp()
    {
        $this->dataCollectorMock = $this
            ->getMockBuilder(DataCollector::class)
            ->disableOriginalConstructor()
            ->getMock();
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
        $formatterMock = $this->getMockBuilder(Formatter::class)->getMock();
        $formatterMock
            ->expects(static::once())
            ->method('format')
            ->withConsecutive($this->equalTo($debugData))
            ->willReturn($formattedDebugData);
        
        $debugDumperMock = $this->getMockBuilder(DebugDumper::class)->getMock();
        $debugDumperMock
            ->expects(static::once())
            ->method('dump')
            ->withConsecutive($this->equalTo($formattedDebugData));
        
        $this->systemUnderTest->debug($formatterMock, $debugDumperMock);
    }
}