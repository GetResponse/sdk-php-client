<?php

namespace Getresponse\Sdk\Client\Test\Unit\Operation;

use Getresponse\Sdk\Client\Operation\FailedOperationResponse;
use Getresponse\Sdk\Client\Operation\OperationResponse;
use Getresponse\Sdk\Client\Operation\OperationResponseCollection;
use Getresponse\Sdk\Client\Operation\SuccessfulOperationResponse;

/**
 * Class OperationResponseCollectionTest
 * @package Getresponse\Sdk\Client\Test\Unit\Operation
 */
class OperationResponseCollectionTest extends \PHPUnit_Framework_TestCase
{
    /** @var OperationResponseCollection */
    private $systemUnderTest;
    
    /**
     * @inheritDoc
     */
    protected function setUp()
    {
        $operations = [
            $this->prophesize(SuccessfulOperationResponse::class)->reveal(),
            $this->prophesize(SuccessfulOperationResponse::class)->reveal(),
            $this->prophesize(FailedOperationResponse::class)->reveal(),
        ];
        $succeeded = [
            $this->prophesize(SuccessfulOperationResponse::class)->reveal(),
            $this->prophesize(SuccessfulOperationResponse::class)->reveal()
        ];
        $failed = [
            $this->prophesize(FailedOperationResponse::class)->reveal()
        ];
        $this->systemUnderTest = new OperationResponseCollection($operations, $succeeded, $failed);
    }
    
    /**
     * @test
     */
    public function shouldGetIterator()
    {
        self::assertContainsOnlyInstancesOf(OperationResponse::class, $this->systemUnderTest);
    }
    
    /**
     * @test
     */
    public function shouldGetAll()
    {
        self::assertContainsOnlyInstancesOf(OperationResponse::class, $this->systemUnderTest->getAll());
    }
    
    /**
     * @test
     */
    public function shouldGetFailedOperations()
    {
        self::assertTrue($this->systemUnderTest->hasFailures());
        self::assertContainsOnlyInstancesOf(
            FailedOperationResponse::class,
            $this->systemUnderTest->getFailedOperations()
        );
    }
    
    /**
     * @test
     */
    public function shouldGetSucceededOperations()
    {
        $operations = [
            $this->prophesize(SuccessfulOperationResponse::class)->reveal(),
            $this->prophesize(SuccessfulOperationResponse::class)->reveal(),
        ];
        $succeeded = [
            $this->prophesize(SuccessfulOperationResponse::class)->reveal(),
            $this->prophesize(SuccessfulOperationResponse::class)->reveal()
        ];
        $failed = [];
        $systemUnderTest = new OperationResponseCollection($operations, $succeeded, $failed);
        
        self::assertTrue($this->systemUnderTest->hasFailures());
        self::assertContainsOnlyInstancesOf(
            SuccessfulOperationResponse::class,
            $systemUnderTest->getSucceededOperations()
        );
    }
}
