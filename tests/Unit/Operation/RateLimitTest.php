<?php

namespace Getresponse\Sdk\Client\Test\Unit\Operation;

use Getresponse\Sdk\Client\Operation\RateLimit;
use PHPUnit\Framework\TestCase;

/**
 * Class RateLimitTest
 * @package Getresponse\Sdk\Client\Test\Unit\Operation
 */
class RateLimitTest extends TestCase
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
     */
    public function shouldThrowInvalidRateLimitDataExceptionIfResetIsNotValid()
    {
        $this->expectException(\Getresponse\Sdk\Client\Exception\InvalidRateLimitData::class);
        $this->expectExceptionMessage('RateLimit-Reset invalid: -123');
        $rateLimit = new RateLimit('30000', '29995', '-123 seconds');

        $rateLimit->getTimeFrameResetDateTime(new \DateTimeImmutable('now'));
    }
}
