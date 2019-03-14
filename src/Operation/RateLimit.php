<?php

namespace Getresponse\Sdk\Client\Operation;

use Getresponse\Sdk\Client\Exception\InvalidRateLimitData;

/**
 * Class RateLimit
 * @package Getresponse\Sdk\Client\Operation
 */
class RateLimit
{
    /**
     * Total number of requests available per time frame (typically 600 seconds)
     * @var int
     */
    private $limit;

    /**
     * Number of requests left in the current time frame
     * @var int
     */
    private $remaining;

    /**
     * Seconds left in the current time frame
     * @var int
     */
    private $reset;

    /**
     * RateLimit constructor.
     * @param int $limit
     * @param int $remaining
     * @param int $reset
     */
    public function __construct($limit, $remaining, $reset)
    {
        $this->limit = (int) $limit;
        $this->remaining = (int) $remaining;
        $this->reset = (int) $reset;
    }

    /**
     * Total number of requests available per time frame (typically 600 seconds)
     * @return int
     */
    public function getLimit()
    {
        return $this->limit;
    }

    /**
     * Number of requests left in the current time frame
     * @return int
     */
    public function getRemaining()
    {
        return $this->remaining;
    }

    /**
     * Seconds left in the current time frame
     * @return int
     */
    public function getReset()
    {
        return $this->reset;
    }

    /**
     * Given the request timestamp, the method will calculate when the rate limit time frame resets
     *
     * @param \DateTimeImmutable $requestDateTime
     * @return \DateTimeImmutable
     * @throws InvalidRateLimitData
     */
    public function getTimeFrameResetDateTime(\DateTimeImmutable $requestDateTime)
    {
        try {
            $interval = new \DateInterval('PT' . $this->reset . 'S');
        } catch (\Exception $e) {
            throw new InvalidRateLimitData('RateLimit-Reset invalid: ' . $this->reset);
        }

        return $requestDateTime->add($interval);
    }
}
