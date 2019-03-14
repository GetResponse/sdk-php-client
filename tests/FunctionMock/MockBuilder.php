<?php
namespace Getresponse\Sdk\Client\Test\FunctionMock;

use phpmock\functions\FunctionProvider;

/**
 * Class FunctionMock
 * @package Getresponse\Sdk\Client\Test\FunctionMock
 */
class MockBuilder
{
    /** @var string */
    private $namespace;
    
    /** @var string */
    private $name;
    
    /** @var callable */
    private $function;
    
    /**
     * @param string $namespace
     * @return $this
     */
    public function setNamespace($namespace)
    {
        $this->namespace = $namespace;
        return $this;
    }
    
    /**
     * @param string $name
     * @return $this
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }
    
    /**
     * @param callable $function
     * @return $this
     */
    public function setFunction(callable $function)
    {
        $this->function = $function;
        return $this;
    }
    
    /**
     * @param FunctionProvider $provider
     * @return MockBuilder
     */
    public function setFunctionProvider(FunctionProvider $provider)
    {
        return $this->setFunction($provider->getCallable());
    }
    
    /**
     * @return FunctionMock
     */
    public function build()
    {
        $functionMock = FunctionMockRegistry::get($this->namespace, $this->name);
        $functionMock->overwriteCallback($this->function);
        return $functionMock;
    }
}