<?php

namespace Getresponse\Sdk\Client\Test\Unit\Handler\Call;

use Getresponse\Sdk\Client\Handler\Call\CallInfo;

/**
 * Class CallInfoTest
 * @package Unit\Handler
 */
class CallInfoTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function shouldBeImmutable()
    {
        $systemUnderTest = new CallInfo();
        
        $withRequestSize = $systemUnderTest->withRequestSize(100);
        self::assertNull($systemUnderTest->getRequestSize());
        self::assertEquals(100, $withRequestSize->getRequestSize());
        
        $withSpeedDownload = $systemUnderTest->withSpeedDownload(2000);
        self::assertNull($systemUnderTest->getSpeedDownload());
        self::assertEquals(2000, $withSpeedDownload->getSpeedDownload());
        
        $withConnectTime = $systemUnderTest->withConnectTime(123);
        self::assertNull($systemUnderTest->getConnectTime());
        self::assertEquals(123, $withConnectTime->getConnectTime());
        
        $withTotalTime = $systemUnderTest->withTotalTime(253);
        self::assertNull($systemUnderTest->getTotalTime());
        self::assertEquals(253, $withTotalTime->getTotalTime());
        
        $withSizeDownload = $systemUnderTest->withSizeDownload(650);
        self::assertNull($systemUnderTest->getSizeDownload());
        self::assertEquals(650, $withSizeDownload->getSizeDownload());
    }
}
