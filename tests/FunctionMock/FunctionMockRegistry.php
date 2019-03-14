<?php
namespace Getresponse\Sdk\Client\Test\FunctionMock;

/**
 * Class FunctionMockRegistry
 * @package Getresponse\Sdk\Client\Test\FunctionMock
 */
class FunctionMockRegistry
{
    /** @var array | FunctionMock[] */
    private static $registry = [];
    
    /**
     * @param FunctionMock $functionMock
     */
    public static function register(FunctionMock $functionMock)
    {
        self::$registry[$functionMock->getNameWithNamespace()] = $functionMock;
    }
    
    /**
     * @param string $namespace
     * @param string $name
     * @return FunctionMock
     */
    public static function get($namespace, $name)
    {
        $nameWithNamespace = $namespace . '\\' . $name;
        if (empty(self::$registry[$nameWithNamespace])) {
            FunctionMockFactory::create($namespace, $name);
        }
        return self::$registry[$nameWithNamespace];
    }
    
    /**
     * @return void
     */
    public static function resetAll()
    {
        foreach (self::$registry as $functionMock) {
            $functionMock->reset();
        }
    }
}