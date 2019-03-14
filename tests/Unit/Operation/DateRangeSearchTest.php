<?php

namespace Getresponse\Sdk\Client\Test\Unit\Operation;

use Getresponse\Sdk\Client\Operation\DateRangeSearch;

/**
 * Class DateRangeSearchTest
 * @package Getresponse\Sdk\Client\Test\Unit\Operation
 */
class DateRangeSearchTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function shouldReturnOnlyFilledParametersInArray()
    {
        $emptyRange = new DateRangeSearch();
        self::assertEquals([], $emptyRange->toArray());

        $onlyFromRange = new DateRangeSearch('2017-01-01T00:00:00+0000');
        self::assertEquals(['from' => '2017-01-01T00:00:00+0000'], $onlyFromRange->toArray());

        $onlyToRange = new DateRangeSearch(null, '2017-12-31T00:00:00+0000');
        self::assertEquals(['to' => '2017-12-31T00:00:00+0000'], $onlyToRange->toArray());

        $completeRange = new DateRangeSearch('2017-01-01T00:00:00+0000', '2017-12-31T00:00:00+0000');
        self::assertEquals(
            ['from' => '2017-01-01T00:00:00+0000', 'to' => '2017-12-31T00:00:00+0000'],
            $completeRange->toArray()
        );
    }
}
