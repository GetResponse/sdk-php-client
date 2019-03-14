<?php
namespace Getresponse\Sdk\Client\Test\Unit\Debugger;

use Getresponse\Sdk\Client\Debugger\JsonStreamReader;
use Getresponse\Sdk\Client\Test\FunctionMock\FunctionMockRegistry;
use Psr\Http\Message\StreamInterface;
use Getresponse\Sdk\Client\Test\FunctionMock\MockBuilder;

/**
 * Class JsonStreamReaderTest
 * @package Getresponse\Sdk\Client\Test\Unit\Debugger
 */
class JsonStreamReaderTest extends \PHPUnit_Framework_TestCase
{
    const DEBUG_DUMPER_NAMESPACE = 'Getresponse\Sdk\Client\Debugger';
    
    /** @var JsonStreamReader */
    private $systemUnderTest;
    
    protected function setUp()
    {
        FunctionMockRegistry::resetAll();
        $this->systemUnderTest = new JsonStreamReader();
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
    public function shouldRead()
    {
        $streamMock = $this
            ->getMockBuilder(StreamInterface::class)
            ->getMock();
        
        $streamMock
            ->expects(static::once())
            ->method('getContents')
            ->willReturn('{"name":"contact","contactId":"gytUi"}');
        
        $body = $this->systemUnderTest->read($streamMock);
        static::assertEquals(['name' => 'contact', 'contactId' => 'gytUi'], $body);
    }
    
    /**
     * @test
     * @expectedException \Getresponse\Sdk\Client\Debugger\StreamReaderException
     */
    public function shouldThrowExceptionWhenJSONExtensionNotLoaded()
    {
        $streamMock = $this
            ->getMockBuilder(StreamInterface::class)
            ->getMock();
        
        $streamMock
            ->expects(static::never())
            ->method('getContents');
        
        (new MockBuilder())
            ->setNamespace(self::DEBUG_DUMPER_NAMESPACE)
            ->setName('extension_loaded')
            ->setFunction(function ($value) {
                static::assertEquals('json', $value);
                return false;
            })
            ->build();
        
        $this->systemUnderTest->read($streamMock);
    }
}