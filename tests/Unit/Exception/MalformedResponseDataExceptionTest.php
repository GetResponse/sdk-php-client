<?php

namespace Getresponse\Sdk\Client\Test\Unit\Exception;

use Getresponse\Sdk\Client\Exception\MalformedResponseDataException;
use Getresponse\Sdk\Client\Test\FunctionMock\FunctionMockRegistry;
use Getresponse\Sdk\Client\Test\FunctionMock\MockBuilder;
use phpmock\functions\FixedValueFunction;
use PHPUnit\Framework\TestCase;

/**
 * Class MalformedResponseDataExceptionTest
 * @package Getresponse\Sdk\Client\Test\Unit\Exception
 */
class MalformedResponseDataExceptionTest extends TestCase
{
    protected function setUp(): void
    {
        FunctionMockRegistry::resetAll();
    }
    
    /**
     * @inheritDoc
     */
    protected function tearDown(): void
    {
        FunctionMockRegistry::resetAll();
    }
    
    /**
     * @test
     */
    public function shouldCreateExceptionFromJsonLastErrorMsg()
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

        $exception = MalformedResponseDataException::createFromJsonLastErrorMsg('malformed JSON');

        self::assertEquals('Invalid JSON: Syntax error (4) Data: malformed JSON', $exception->getMessage());
    }
}
