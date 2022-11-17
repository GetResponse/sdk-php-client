<?php

namespace Getresponse\Sdk\Client\Test\Unit\Operation;

use Getresponse\Sdk\Client\Operation\SearchQuery;
use PHPUnit\Framework\TestCase;

/**
 * Class SearchQueryTest
 * @package Getresponse\Sdk\Client\Test\Unit\Operation
 */
class SearchQueryTest extends TestCase
{
    /**
     * @var SearchQuery
     */
    private $systemUnderTest;

    protected function setUp(): void
    {
        $this->systemUnderTest = new SearchQueryImplementation();
    }

    /**
     * @test
     */
    public function shouldThrowInvalidArgumentExceptionWhenTryingToSearchByInvalidField()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid search query field');
        $this->systemUnderTest->set('campaign', 'abcd');
    }

    /**
     * @test
     * @doesNotPerformAssertions
     */
    public function shouldAllowSearchingByAllowedFields()
    {
        $this->systemUnderTest->set('name', 'Example');

        $this->systemUnderTest->set('email', 'example@example.com');
    }

    /**
     * @test
     */
    public function shouldReturnSearchQueryAsArray()
    {
        $this->systemUnderTest
            ->set('name', 'Example')
            ->set('email', 'example@example.com');

        self::assertEquals(['name' => 'Example', 'email' => 'example@example.com'], $this->systemUnderTest->toArray());
    }
}
