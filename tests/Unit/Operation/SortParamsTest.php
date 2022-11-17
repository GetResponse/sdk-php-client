<?php

namespace Getresponse\Sdk\Client\Test\Unit\Operation;

use Getresponse\Sdk\Client\Operation\SortParams;
use PHPUnit\Framework\TestCase;

/**
 * Class SortParamsTest
 * @package Getresponse\Sdk\Client\Test\Unit\Operation
 */
class SortParamsTest extends TestCase
{
    /**
     * @var SortParams
     */
    private $systemUnderTest;

    protected function setUp(): void
    {
        $this->systemUnderTest = new SortParamsImplementation();
    }

    /**
     * @test
     */
    public function shouldThrowInvalidArgumentExceptionForDirectionOtherThanAscOrDesc()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Not allowed sort direction');
        $this->systemUnderTest->sortBy('email', 'up');
    }

    /**
     * @test
     * @doesNotPerformAssertions
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
     */
    public function shouldThrowInvalidArgumentExceptionWhenTryingToSortByInvalidField()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Not allowed sort param');
        $this->systemUnderTest->sortBy('campaign', 'asc');
    }

    /**
     * @test
     */
    public function shouldThrowInvalidArgumentExceptionWhenTryingToSortAscByInvalidField()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Not allowed sort param');
        $this->systemUnderTest->sortAscBy('campaign');
    }

    /**
     * @test
     */
    public function shouldThrowInvalidArgumentExceptionWhenTryingToSortDescByInvalidField()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Not allowed sort param');
        $this->systemUnderTest->sortDescBy('campaign');
    }

    /**
     * @test
     * @doesNotPerformAssertions
     */
    public function shouldAllowToSortAscByAllowedField()
    {
        $this->systemUnderTest->sortAscBy('email');

        $this->systemUnderTest->sortAscBy('name');
    }

    /**
     * @test
     * @doesNotPerformAssertions
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
