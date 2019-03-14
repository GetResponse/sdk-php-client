<?php

namespace Getresponse\Sdk\Client\Test\Unit\Operation;


/**
 * Class ValueListTest
 * @package Getresponse\Sdk\Client\Test\Unit\Operation
 */
class ValueListTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Invalid values: fromField, status
     */
    public function shouldThrowExceptionForInvalidValues()
    {
        new ValueListImplementation('name', 'email', 'fromField', 'status');
    }

    /**
     * @test
     */
    public function shouldReturnValuesAsCommaSeparatedString()
    {
        $systemUnderTest = new ValueListImplementation('name', 'email', 'campaign');

        self::assertEquals('name,email,campaign', $systemUnderTest->toString());
    }
}
