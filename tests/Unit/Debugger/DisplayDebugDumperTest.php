<?php
namespace Getresponse\Sdk\Client\Test\Unit\Debugger;

use Getresponse\Sdk\Client\Debugger\DisplayDebugDumper;
use PHPUnit\Framework\TestCase;

/**
 * Class DisplayDebugDumperTest
 * @package Getresponse\Sdk\Client\Test\Unit\Debugger
 */
class DisplayDebugDumperTest extends TestCase
{
    /**
     * @var DisplayDebugDumper
     */
    private $systemUnderTest;
    
    protected function setUp(): void
    {
        $this->systemUnderTest = new DisplayDebugDumper();
    }
    
    /**
     * @test
     */
    public function shouldDisplay()
    {
        ob_start();
        $this->systemUnderTest->dump('DUMPING CONTENT');
        static::assertEquals('DUMPING CONTENT' . PHP_EOL, ob_get_contents());
        ob_end_clean();
    }
}