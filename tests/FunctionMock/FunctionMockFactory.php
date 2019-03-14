<?php
namespace Getresponse\Sdk\Client\Test\FunctionMock;

use phpmock\MockBuilder;

/**
 * Class FunctionMock
 * @package Getresponse\Sdk\Client\Test\FunctionMock
 */
class FunctionMockFactory
{
    /**
     * @param string $namespace
     * @param string $name
     * @param callable|null $callback
     * @return FunctionMock
     */
    public static function create($namespace, $name, callable $callback = null)
    {
        $fullyQualifiedName = $namespace . '\\' . $name;
        $parameterBuilder = new ParameterBuilder();
        $parameterBuilder->build($name);
        $data = [
            'namespace' => $namespace,
            'name' => $name,
            'fqfn' => $fullyQualifiedName,
            'invokeSignatureParameters' => $parameterBuilder->getSignatureParameters(),
            'bodyParameters' => $parameterBuilder->getBodyParameters(),
        ];
        $template = new \Text_Template(__DIR__ . '/functionMockClassTemplate.tpl');
        $template->setVar($data, false);
        $definition = $template->render();
        eval($definition);
    
        if (null === $callback) {
            $defaultCallbackDefinition = self::createDefaultCallbackDefinition($namespace, $name, $parameterBuilder);
            eval($defaultCallbackDefinition);
        }
        $functionMockClassName = $fullyQualifiedName . '\\FunctionMock';
        /** @var $functionMock FunctionMock */
        FunctionMockRegistry::register($functionMock = new $functionMockClassName($namespace, $name, $callback));
        
        (new MockBuilder())
            ->setNamespace($namespace)
            ->setName($name)
            ->setFunction($functionMock->getCallable())
            ->build()
            ->enable();
        
        return $functionMock;
    }
    
    /**
     * @param string $namespace
     * @param string $name
     * @param ParameterBuilder $parameterBuilder
     * @return string
     */
    private static function createDefaultCallbackDefinition($namespace, $name, ParameterBuilder $parameterBuilder)
    {
        $fullyQualifiedName = $namespace . '\\' . $name;
        $data = [
            'namespace' => $namespace,
            'name' => $name,
            'fqfn' => $fullyQualifiedName,
            'signatureParameters' => $parameterBuilder->getSignatureParameters(),
            'bodyParameters' => $parameterBuilder->getBodyParameters(),
        ];
        $template = new \Text_Template(__DIR__ . '/function.tpl');
        $template->setVar($data, false);
        return $template->render();
    }
}