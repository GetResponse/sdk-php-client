<?php

namespace Getresponse\Sdk\Client\Test\Unit\Exception;

use Getresponse\Sdk\Client\Exception\InvalidCommandDataException;
use Getresponse\Sdk\Client\Test\FunctionMock\FunctionMockRegistry;
use Getresponse\Sdk\Client\Test\FunctionMock\MockBuilder;
use phpmock\functions\FixedValueFunction;

/**
 * Class InvalidCommandDataExceptionTest
 * @package Getresponse\Sdk\Client\Test\Unit\Exception
 */
class InvalidCommandDataExceptionTest extends \PHPUnit_Framework_TestCase
{
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


        $exception = InvalidCommandDataException::createFromJsonLastErrorMsg(['a' => 1, 'b' => 'c']);

        self::assertEquals('Invalid data: Syntax error (4) Dump: ' . "Array\n(\n    [a] => 1\n    [b] => c\n)\n", $exception->getMessage());
    }

    /**
     * @test
     */
    public function shouldCreateExceptionFromInvalidField()
    {
        $exception = InvalidCommandDataException::createFromInvalidField('newsletterId', 'CreateContact');

        self::assertEquals('Field newsletterId is not available for command CreateContact', $exception->getMessage());
    }

    /**
     * @test
     */
    public function shouldCreateExceptionFromMissingFieldsList()
    {
        $exception = InvalidCommandDataException::createFromMissingFieldsList(['email', 'campaign'], 'CreateContact');

        self::assertEquals(
            'Command CreateContact is missing required fields: email, campaign',
            $exception->getMessage()
        );
    }
}
