<?php
namespace Getresponse\Sdk\Client\Operation;

/**
 * Class OperationResponseCollector
 * @package Getresponse\Sdk\Client\Operation
 */
class OperationResponseCollector
{
    /** @var array | OperationResponse[] */
    private $operations = [];
    
    /** @var array | SuccessfulOperationResponse[] */
    private $succeeded = [];
    
    /** @var array | FailedOperationResponse[] */
    private $failed = [];
    
    /**
     * @param OperationResponse $operationResponse
     */
    public function collect(OperationResponse $operationResponse)
    {
        $this->operations[] = $operationResponse;
        if ($operationResponse->isSuccess()) {
            $this->succeeded[] = $operationResponse;
        } else {
            $this->failed[] = $operationResponse;
        }
    }
    
    /**
     * @return OperationResponseCollection
     */
    public function getCollection()
    {
        return new OperationResponseCollection($this->operations, $this->succeeded, $this->failed);
    }
}