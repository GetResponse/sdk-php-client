<?php

namespace Getresponse\Sdk\Client\Test\Unit\Handler;

use Getresponse\Sdk\Client\Handler\CurlCallInfoFactory;

/**
 * Class CurlCallInfoFactoryTest
 * @package Unit\Handler
 */
class CurlCallInfoFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function createByInfo()
    {
        $callInfo = CurlCallInfoFactory::createFromInfo([
            'connect_time' => 200,
            'request_size' => 300,
            'size_download' => 2048,
            'speed_download' => 8096,
            'total_time' => 123,
        ]);
        self::assertEquals(200, $callInfo->getConnectTime());
        self::assertEquals(300, $callInfo->getRequestSize());
        self::assertEquals(2048, $callInfo->getSizeDownload());
        self::assertEquals(8096, $callInfo->getSpeedDownload());
        self::assertEquals(123, $callInfo->getTotalTime());
    }
    /**
     * @test
     */
    public function shouldReturnNullIfNoInfoData()
    {
        self::assertNull(CurlCallInfoFactory::createFromInfo(false));
    }
}
