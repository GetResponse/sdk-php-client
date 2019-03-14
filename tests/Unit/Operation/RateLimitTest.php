<?php

namespace Getresponse\Sdk\Client\Test\Unit\Operation;

use Getresponse\Sdk\Client\Operation\RateLimit;

/**
 * Class RateLimitTest
 * @package Getresponse\Sdk\Client\Test\Unit\Operation
 */
class RateLimitTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function shouldBeAbleToCalculateTimeFrameResetDateTime()
    {
        $rateLimit = new RateLimit('30000', '29995', '100 seconds');
        $requestDateTime = new \DateTimeImmutable('now');

        $timeFrameResetDateTime = $rateLimit->getTimeFrameResetDateTime($requestDateTime);

        $resultDiff = $timeFrameResetDateTime->diff($requestDateTime);

        self::assertEquals(1, $resultDiff->i);
        self::assertEquals(40, $resultDiff->s);
    }

    /**
     * @test
     * @expectedException \Getresponse\Sdk\Client\Exception\InvalidRateLimitData
     * @expectedExceptionMessage RateLimit-Reset invalid: -123
     */
    public function shouldThrowInvalidRateLimitDataExceptionIfResetIsNotValid()
    {
        $rateLimit = new RateLimit('30000', '29995', '-123 seconds');

        $rateLimit->getTimeFrameResetDateTime(new \DateTimeImmutable('now'));
    }
}
