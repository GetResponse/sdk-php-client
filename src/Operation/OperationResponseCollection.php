<?php
namespace Getresponse\Sdk\Client\Operation;

use Traversable;

/**
 * Class OperationResponseCollection
 * @package Getresponse\Sdk\Client\Operation
 */
class OperationResponseCollection implements \IteratorAggregate
{
    /** @var array | OperationResponse[] */
    private $operations = [];
    
    /** @var array | SuccessfulOperationResponse[] */
    private $succeeded = [];
    
    /** @var array | FailedOperationResponse[] */
    private $failed = [];
    
    /**
     * OperationResponseCollection constructor.
     * @param array|OperationResponse[] $operations
     * @param array|SuccessfulOperationResponse[] $succeeded
     * @param array|FailedOperationResponse[] $failed
     */
    public function __construct(array $operations, array $succeeded, array $failed)
    {
        $this->operations = $operations;
        $this->succeeded = $succeeded;
        $this->failed = $failed;
    }
    
    /**
     * @return \ArrayIterator |  OperationResponse[]
     */
    public function getIterator(): Traversable
    {
        return new \ArrayIterator($this->operations);
    }
    
    /**
     * @return array | OperationResponse[]
     */
    public function getAll()
    {
        return $this->operations;
    }
    
    /**
     * @return bool
     */
    public function hasFailures()
    {
        return !empty($this->failed);
    }
    
    /**
     * @return array | SuccessfulOperationResponse[]
     */
    public function getSucceededOperations()
    {
        return $this->succeeded;
    }
    
    /**
     * @return array | FailedOperationResponse[]
     */
    public function getFailedOperations()
    {
        return $this->failed;
    }
    
}