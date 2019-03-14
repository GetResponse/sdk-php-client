<?php

namespace Getresponse\Sdk\Client\Operation;

/**
 * Class DateRangeSearch
 * @package Getresponse\Sdk\Client\Operation
 */
class DateRangeSearch
{
    /**
     * @var string
     */
    private $from;

    /**
     * @var string
     */
    private $to;

    /**
     * DateRangeSearch constructor.
     * @param string | null $from
     * @param string | null $to
     */
    public function __construct($from = null, $to = null)
    {
        $this->from = $from;
        $this->to = $to;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return array_filter([
            'from' => $this->from,
            'to' => $this->to
        ], 'strlen');
    }
}
