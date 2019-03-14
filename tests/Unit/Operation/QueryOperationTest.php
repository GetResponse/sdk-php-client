<?php

namespace Getresponse\Sdk\Client\Test\Unit\Operation;

use Getresponse\Sdk\Client\Operation\Pagination;

/**
 * Class QueryOperationTest
 * @package Getresponse\Sdk\Client\Test\Unit\Operation
 */
class QueryOperationTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var QueryOperationImplementation
     */
    private $systemUnderTest;

    protected function setUp()
    {
        $this->systemUnderTest = new QueryOperationImplementation();
    }

    /**
     * @test
     */
    public function shouldReturnGetAsOperationMethod()
    {
        self::assertEquals('GET', $this->systemUnderTest->getMethod());
    }

    /**
     * @test
     */
    public function shouldReturn200AsOperationSuccessCode()
    {
        self::assertEquals(200, $this->systemUnderTest->getSuccessCode());
    }

    /**
     * @test
     */
    public function shouldReturnEmptyStringAsOperationBody()
    {
        self::assertEquals('', $this->systemUnderTest->getBody());
    }

    /**
     * @test
     */
    public function shouldReturnUrlWithoutQueryString()
    {
        self::assertEquals('/some-url/123', $this->systemUnderTest->getUrl());
    }

    /**
     * @test
     */
    public function shouldReturnUrlWhenEmptyDefaultValues()
    {
        self::assertEquals('/some-url/123', $this->systemUnderTest->getUrlWithEmptyDefaultValues());
    }


    /**
     * @test
     */
    public function shouldReturnUrlWithQueryString()
    {
        $sortParams = new SortParamsImplementation();
        $sortParams
            ->sortDescBy('email')
            ->sortAscBy('name');

        $searchQuery = new SearchQueryImplementation();
        $searchQuery
            ->set('email', 'example@example.com')
            ->set('name', 'Example');

        $pagination = new Pagination(5, 200);

        $fields = new ValueListImplementation('name', 'email', 'campaign');

        $this->systemUnderTest->setSort($sortParams);
        $this->systemUnderTest->setQuery($searchQuery);
        $this->systemUnderTest->setPagination($pagination);
        $this->systemUnderTest->setFields($fields);

        self::assertEquals(
            '/some-url/123?query%5Bemail%5D=example%40example.com&query%5Bname%5D=Example' .
            '&sort%5Bemail%5D=desc&sort%5Bname%5D=asc' .
            '&page=5&perPage=200&fields=name%2Cemail%2Ccampaign',
            $this->systemUnderTest->getUrl()
        );
    }
}
