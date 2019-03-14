<?php
namespace Getresponse\Sdk\Client\Test\FunctionMock;

/**
 * Interface FunctionMock
 * @package Getresponse\Sdk\Client\Test\FunctionMock
 */
interface FunctionMock
{
    /**
     * @return string
     */
    public function getNameWithNamespace();
    
    /**
     * @param callable $callback
     * @return $this
     */
    public function overwriteCallback(callable $callback);
    
    /**
     * @return void
     */
    public function reset();
    
    /**
     * @return callable
     */
    public function getCallable();
}