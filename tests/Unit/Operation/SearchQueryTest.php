<?php

namespace Getresponse\Sdk\Client\Test\Unit\Operation;

use Getresponse\Sdk\Client\Operation\SearchQuery;

/**
 * Class SearchQueryTest
 * @package Getresponse\Sdk\Client\Test\Unit\Operation
 */
class SearchQueryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var SearchQuery
     */
    private $systemUnderTest;

    protected function setUp()
    {
        $this->systemUnderTest = new SearchQueryImplementation();
    }

    /**
     * @test
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Invalid search query field
     */
    public function shouldThrowInvalidArgumentExceptionWhenTryingToSearchByInvalidField()
    {
        $this->systemUnderTest->set('campaign', 'abcd');
    }

    /**
     * @test
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
