<?php

namespace Getresponse\Sdk\Client\Test\Unit\Operation;

use Getresponse\Sdk\Client\Operation\SortParams;

/**
 * Class SortParamsTest
 * @package Getresponse\Sdk\Client\Test\Unit\Operation
 */
class SortParamsTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var SortParams
     */
    private $systemUnderTest;

    protected function setUp()
    {
        $this->systemUnderTest = new SortParamsImplementation();
    }

    /**
     * @test
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Not allowed sort direction
     */
    public function shouldThrowInvalidArgumentExceptionForDirectionOtherThanAscOrDesc()
    {
        $this->systemUnderTest->sortBy('email', 'up');
    }

    /**
     * @test
     */
    public function shouldAllowAscAndDescSortDirections()
    {
        $this->systemUnderTest->sortBy('email', 'asc');

        $this->systemUnderTest->sortBy('email', 'desc');

        $this->systemUnderTest->sortBy('email', 'ASC');

        $this->systemUnderTest->sortBy('email', 'DESC');

        $this->systemUnderTest->sortBy('email', 'Asc');

        $this->systemUnderTest->sortBy('email', 'Desc');
    }

    /**
     * @test
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Not allowed sort param
     */
    public function shouldThrowInvalidArgumentExceptionWhenTryingToSortByInvalidField()
    {
        $this->systemUnderTest->sortBy('campaign', 'asc');
    }

    /**
     * @test
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Not allowed sort param
     */
    public function shouldThrowInvalidArgumentExceptionWhenTryingToSortAscByInvalidField()
    {
        $this->systemUnderTest->sortAscBy('campaign');
    }

    /**
     * @test
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Not allowed sort param
     */
    public function shouldThrowInvalidArgumentExceptionWhenTryingToSortDescByInvalidField()
    {
        $this->systemUnderTest->sortDescBy('campaign');
    }

    /**
     * @test
     */
    public function shouldAllowToSortAscByAllowedField()
    {
        $this->systemUnderTest->sortAscBy('email');

        $this->systemUnderTest->sortAscBy('name');
    }

    /**
     * @test
     */
    public function shouldAllowToSortDescByAllowedField()
    {
        $this->systemUnderTest->sortDescBy('email');

        $this->systemUnderTest->sortDescBy('name');
    }

    /**
     * @test
     */
    public function shouldReturnSortParamsAsArray()
    {
        $this->systemUnderTest
            ->sortAscBy('email')
            ->sortDescBy('name');

        self::assertEquals(['email' => 'asc', 'name' => 'desc'], $this->systemUnderTest->toArray());
    }
}
