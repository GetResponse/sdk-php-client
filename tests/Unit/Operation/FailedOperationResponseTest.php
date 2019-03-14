<?php

namespace Getresponse\Sdk\Client\Test\Unit\Operation;

use Getresponse\Sdk\Client\Exception\ExceptionFactory;
use Getresponse\Sdk\Client\Operation\FailedOperationResponse;
use Getresponse\Sdk\Client\Test\FunctionMock\FunctionMockRegistry;
use Getresponse\Sdk\Client\Test\FunctionMock\MockBuilder;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use phpmock\functions\FixedValueFunction;

/**
 * Class FailedOperationResponseTest
 * @package Getresponse\Sdk\Client\Test\Unit\Operation
 */
class FailedOperationResponseTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @inheritDoc
     */
    protected function setUp()
    {
        FunctionMockRegistry::resetAll();
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
        $response = new Response(400, [], '{"message":"Error Message"}');
        $systemUnderTest = FailedOperationResponse::createWithResponse($response);
        self::assertEquals((string)$response->getBody(), (string)$systemUnderTest->getResponse()->getBody());
    }

    /**
     * @test
     */
    public function shouldReturnErrorMessageFromResponse()
    {
        $response = new Response(400, [], '{"message":"Error Message"}');
        $systemUnderTest = FailedOperationResponse::createWithResponse($response);
        self::assertEquals('Error Message', $systemUnderTest->getErrorMessage());
    }

    /**
     * @test
     */
    public function shouldReturnErrorMessageFromException()
    {
        $request = new Request('GET', 'https://api.getresponse.com/v3/accounts');
        $systemUnderTest = FailedOperationResponse::createWithException(
            ExceptionFactory::exceptionFrom(500, $request, 'Some Exception message', [], '1.0')
        );
        self::assertRegExp('/some exception message/i', $systemUnderTest->getErrorMessage());
    }

    /**
     * @test
     */
    public function shouldReturnResponseStatusCode()
    {
        $response = new Response(400, [], '{"message":"Error Message"}');
        $systemUnderTest = FailedOperationResponse::createWithResponse($response);
        self::assertEquals($response->getStatusCode(), $systemUnderTest->getResponse()->getStatusCode());
    }

    /**
     * @test
     */
    public function shouldReturnDecodedData()
    {
        $response = new Response(400, [], '{"message":"Error Message"}');
        $systemUnderTest = FailedOperationResponse::createWithResponse($response);
        self::assertEquals(['message' => 'Error Message'], $systemUnderTest->getData());
    }

    /**
     * @test
     * @expectedException \Getresponse\Sdk\Client\Exception\MalformedResponseDataException
     * @expectedExceptionMessage Invalid JSON: Syntax error (4) Data: {"message":"Error Message"}
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

        $response = new Response(400, [], '{"message":"Error Message"}');
        $systemUnderTest = FailedOperationResponse::createWithResponse($response);
        $systemUnderTest->getData();
    }

    /**
     * @test
     */
    public function shouldReturnWrappedResponse()
    {
        $response = new Response(400, [], '{"message":"Error Message"}');
        $systemUnderTest = FailedOperationResponse::createWithResponse($response);
        self::assertSame($response, $systemUnderTest->getResponse());
    }

    /**
     * @test
     */
    public function shouldBeAbleToTellWhetherResponseIsPaginated()
    {
        $response = new Response(400, [], '{"message":"Error Message"}');
        $systemUnderTest = FailedOperationResponse::createWithResponse($response);
        self::assertFalse($systemUnderTest->isPaginated());
    }

    /**
     * @test
     */
    public function shouldReturnRateLimit()
    {
        $response = new Response(
            400,
            [
                'X-RateLimit-Limit' => ['30000'],
                'X-RateLimit-Remaining' => ['29995'],
                'X-RateLimit-Reset' => ['100 seconds']
            ],
            '{"message":"Error Message"}'
        );
        $systemUnderTest = FailedOperationResponse::createWithResponse($response);
        $rateLimit = $systemUnderTest->getRateLimit();

        self::assertEquals(30000, $rateLimit->getLimit());
        self::assertEquals(29995, $rateLimit->getRemaining());
        self::assertEquals(100, $rateLimit->getReset());
    }

    /**
     * @test
     */
    public function shouldReturnNullPaginationValues()
    {
        $response = new Response(400, [], '{"message":"Error Message"}');
        $systemUnderTest = FailedOperationResponse::createWithResponse($response);
        self::assertSame(null, $systemUnderTest->getPaginationValues()->getPage());
        self::assertSame(null, $systemUnderTest->getPaginationValues()->getTotalPages());
        self::assertSame(null, $systemUnderTest->getPaginationValues()->getTotalCount());
    }

}
