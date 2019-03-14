<?php
namespace Getresponse\Sdk\Client\Test\Unit\Debugger;

use Getresponse\Sdk\Client\Debugger\DisplayDebugDumper;

/**
 * Class DisplayDebugDumperTest
 * @package Getresponse\Sdk\Client\Test\Unit\Debugger
 */
class DisplayDebugDumperTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var DisplayDebugDumper
     */
    private $systemUnderTest;
    
    protected function setUp()
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