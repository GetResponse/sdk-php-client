<?php
namespace Getresponse\Sdk\Client\Test\Unit\Debugger;

use Getresponse\Sdk\Client\Debugger\FileDebugDumper;
use Getresponse\Sdk\Client\Test\FunctionMock\FunctionMockRegistry;
use Getresponse\Sdk\Client\Test\FunctionMock\MockBuilder;
use PHPUnit\Framework\TestCase;

/**
 * Class FileDebugDumperTest
 * @package Getresponse\Sdk\Client\Test\Unit\Debugger
 */
class FileDebugDumperTest extends TestCase
{
    const DEBUG_DUMPER_NAMESPACE = 'Getresponse\Sdk\Client\Debugger';
    
    /**
     * @inheritDoc
     */
    protected function setUp(): void
    {
        FunctionMockRegistry::resetAll();
    }
    
    /**
     * @inheritDoc
     */
    protected function tearDown(): void
    {
        FunctionMockRegistry::resetAll();
    }
    
    /**
     * @test
     */
    public function shouldDisplay()
    {
        (new MockBuilder())
            ->setNamespace(self::DEBUG_DUMPER_NAMESPACE)
            ->setName('file_put_contents')
            ->setFunction(function ($filename, $debug) {
                static::assertEquals('DUMPING CONTENT', $debug);
            })
            ->build();
    
        (new FileDebugDumper(dirname(__DIR__)))->dump('DUMPING CONTENT');
    }
    
    /**
     * @test
     */
    public function shouldThrowExceptionWhenInvalidDir()
    {
        $this->expectException(\InvalidArgumentException::class);
        new FileDebugDumper('somename');
    }
}