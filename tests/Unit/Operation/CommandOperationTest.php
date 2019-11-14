<?php

namespace Getresponse\Sdk\Client\Test\Unit\Operation;

use Getresponse\Sdk\Client\Operation\CommandOperation;
use Getresponse\Sdk\Client\Test\FunctionMock\FunctionMockRegistry;
use Getresponse\Sdk\Client\Test\FunctionMock\MockBuilder;
use phpmock\functions\FixedValueFunction;

/**
 * Class CommandOperationTest
 * @package Getresponse\Sdk\Client\Test\Unit\Operation
 */
class CommandOperationTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var CommandOperation | CommandOperationImplementation
     */
    private $systemUnderTest;

    protected function setUp()
    {
        FunctionMockRegistry::resetAll();
        $this->systemUnderTest = new CommandOperationImplementation();
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
     * @expectedException \Getresponse\Sdk\Client\Exception\InvalidCommandDataException
     * @expectedExceptionMessage Field fromField is not available for command Getresponse\Sdk\Client\Test\Unit\Operation\CommandOperationImplementation
     */
    public function shouldThrowInvalidCommandDataExceptionDuringSetWithInvalidField()
    {
        $this->systemUnderTest->set('fromField', uniqid('fromField', true));
    }

    /**
     * @test
     * @expectedException \Getresponse\Sdk\Client\Exception\InvalidCommandDataException
     */
    public function shouldThrowInvalidCommandDataExceptionDuringGetBodyIfItFailsToEncodeJson()
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

        $this->systemUnderTest
            ->set('email', 'example@example.com')
            ->set('campaign', ['campaignId' => 'abcd']);
        $this->systemUnderTest->getBody();
    }

    /**
     * @test
     */
    public function shouldEncodeDataToJson()
    {
        $this->systemUnderTest
            ->set('email', 'example@example.com')
            ->set('campaign', ['campaignId' => 'abcd']);
        
        $encodedData = $this->systemUnderTest->getBody();

        self::assertEquals('{"email":"example@example.com","campaign":{"campaignId":"abcd"}}', $encodedData);
    }

    /**
     * @test
     */
    public function shouldBuildProperUrl()
    {
        $sut = new CommandOperationImplementation();
        $sut->setUrlParameterQuery((new UrlQueryParametersImplementation())->whereFoo('foo_value'));
        
        self::assertEquals(
            '/some-url?foo=foo_value',
            $sut->getUrl()
        );
    
        $sut = new CommandOperationImplementation();
        $sut->setAdditionalFlags(new AdditionalFlagsImplementation('boo'));
    
        self::assertEquals(
            '/some-url?additionalFlags=boo',
            $sut->getUrl()
        );
    
        $sut = new CommandOperationImplementation();
        $sut->setAdditionalFlags(new AdditionalFlagsImplementation('boo'))
            ->setUrlParameterQuery((new UrlQueryParametersImplementation())->whereFoo('foo_value'));
    
        self::assertEquals(
            '/some-url?foo=foo_value&additionalFlags=boo',
            $sut->getUrl()
        );
    }
}
