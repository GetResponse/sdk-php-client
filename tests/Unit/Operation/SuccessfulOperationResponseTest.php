<?php

namespace Getresponse\Sdk\Client\Test\Unit\Operation;

use Getresponse\Sdk\Client\Operation\OperationResponse;
use Getresponse\Sdk\Client\Operation\SuccessfulOperationResponse;
use Getresponse\Sdk\Client\Test\FunctionMock\FunctionMockRegistry;
use Getresponse\Sdk\Client\Test\FunctionMock\MockBuilder;
use GuzzleHttp\Psr7\Response;
use phpmock\functions\FixedValueFunction;
use Psr\Http\Message\ResponseInterface;

/**
 * Class SuccessfulOperationResponseTest
 * @package Getresponse\Sdk\Client\Test\Unit\Operation
 */
class SuccessfulOperationResponseTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ResponseInterface
     */
    private $response;

    /**
     * @var OperationResponse
     */
    private $systemUnderTest;

    protected function setUp()
    {
        FunctionMockRegistry::resetAll();
        $this->response = new Response(
            200,
            [
                'X-RateLimit-Limit' => ['30000'],
                'X-RateLimit-Remaining' => ['29995'],
                'X-RateLimit-Reset' => ['100 seconds']
            ],
            '{"a": 123, "b": "abcd"}'
        );

        $this->systemUnderTest = new SuccessfulOperationResponse($this->response, 200);
    }
    
    /**
     * @inheritDoc
     */
    protected function tearDown()
    {
        FunctionMockRegistry::resetAll();
    }
    
    /**
     * @test
     */
    public function shouldReturnResponseBody()
    {
        self::assertEquals((string) $this->response->getBody(), (string) $this->systemUnderTest->getResponse()->getBody());
    }

    /**
     * @test
     */
    public function shouldReturnResponseStatusCode()
    {
        self::assertEquals($this->response->getStatusCode(), $this->systemUnderTest->getResponse()->getStatusCode());
    }
    
    /**
     * @test
     */
    public function shouldReturnDecodedData()
    {
        self::assertEquals(['a' => 123, 'b' => 'abcd'], $this->systemUnderTest->getData());
    }

    /**
     * @test
     * @expectedException \Getresponse\Sdk\Client\Exception\MalformedResponseDataException
     * @expectedExceptionMessage Invalid JSON: Syntax error (4) Data: {"a": 123, "b": "abcd"}
     */
    public function shouldThrowMalformedResponseDataExceptionIfBodyIsNotAValidJsonString()
    {
        $builder = new MockBuilder();
        $builder
            ->setNamespace('Getresponse\Sdk\Client\Exception')
            ->setName('json_last_error_msg')
            ->setFunctionProvider(new FixedValueFunction('Syntax error'))
            ->build();

        $builder
            ->setNamespace('Getresponse\Sdk\Client\Exception')
            ->setName('json_last_error')
            ->setFunctionProvider(new FixedValueFunction(JSON_ERROR_SYNTAX))
            ->build();

        $builder
            ->setNamespace('Getresponse\Sdk\Client\Operation')
            ->setName('json_last_error')
            ->setFunctionProvider(new FixedValueFunction(JSON_ERROR_SYNTAX))
            ->build();
    
        $this->systemUnderTest->getData();
    }

    /**
     * @test
     */
    public function shouldReturnWrappedResponse()
    {
        self::assertSame($this->response, $this->systemUnderTest->getResponse());
    }

    /**
     * @test
     */
    public function shouldBeAbleToTellWhetherResponseIsPaginated()
    {
        self::assertFalse($this->systemUnderTest->isPaginated());

        $paginatedOperationResponse = new SuccessfulOperationResponse(new Response(200, [
            'TotalCount' => 200,
            'TotalPages' => 5,
            'CurrentPage' => 1,
        ]), 200);
        
        self::assertTrue($paginatedOperationResponse->isPaginated());
    }

    /**
     * @test
     */
    public function shouldReturnPaginationValues()
    {
        $paginatedOperationResponse = new SuccessfulOperationResponse(new Response(200, [
            'TotalCount' => 200,
            'TotalPages' => 5,
            'CurrentPage' => 1,
        ]), 200);

        self::assertSame(200, $paginatedOperationResponse->getPaginationValues()->getTotalCount());
        self::assertSame(5, $paginatedOperationResponse->getPaginationValues()->getTotalPages());
        self::assertSame(1, $paginatedOperationResponse->getPaginationValues()->getPage());
    }

    /**
     * @test
     */
    public function shouldReturnRateLimit()
    {
        $rateLimit = $this->systemUnderTest->getRateLimit();

        self::assertEquals(30000, $rateLimit->getLimit());
        self::assertEquals(29995, $rateLimit->getRemaining());
        self::assertEquals(100, $rateLimit->getReset());
    }
}
