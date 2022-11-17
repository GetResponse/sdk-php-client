<?php

namespace Getresponse\Sdk\Client\Test\Unit\Operation;


use PHPUnit\Framework\TestCase;

/**
 * Class ValueListTest
 * @package Getresponse\Sdk\Client\Test\Unit\Operation
 */
class ValueListTest extends TestCase
{
    /**
     * @test
     */
    public function shouldThrowExceptionForInvalidValues()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid values: fromField, status');
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
