<?php

namespace Getresponse\Sdk\Client\Test\Unit\Operation;

use Getresponse\Sdk\Client\Operation\FailedOperationResponse;
use Getresponse\Sdk\Client\Operation\OperationResponseCollector;
use Getresponse\Sdk\Client\Operation\SuccessfulOperationResponse;

/**
 * Class OperationResponseCollectorTest
 * @package Getresponse\Sdk\Client\Test\Unit\Operation
 */
class OperationResponseCollectorTest extends \PHPUnit_Framework_TestCase
{
    /** @var OperationResponseCollector */
    private $systemUnderTest;
    
    /**
     * @inheritDoc
     */
    protected function setUp()
    {
        $this->systemUnderTest = new OperationResponseCollector();
    }
    
    /**
     * @test
     */
    public function shouldGetCollection()
    {
        $successfulOperationResponseMock = $this->prophesize(SuccessfulOperationResponse::class);
        $successfulOperationResponseMock->isSuccess()->willReturn(true);
        
        $this->systemUnderTest->collect($successfulOperationResponseMock->reveal());
        $this->systemUnderTest->collect($this->prophesize(FailedOperationResponse::class)->reveal());
    
        $operationCollection = $this->systemUnderTest->getCollection();
        
        self::assertCount(2, $operationCollection->getAll());
        self::assertCount(1, $operationCollection->getSucceededOperations());
        self::assertContainsOnlyInstancesOf(
            SuccessfulOperationResponse::class,
            $operationCollection->getSucceededOperations()
        );
        
        self::assertCount(1, $operationCollection->getFailedOperations());
        self::assertContainsOnlyInstancesOf(
            FailedOperationResponse::class,
            $operationCollection->getFailedOperations()
        );
    }
}
