<?php
namespace Getresponse\Sdk\Client\Test\FunctionMock;

/**
 * Class ParameterBuilder based on phpmock\generator\ParameterBuilder
 * @package Getresponse\Sdk\Client\Test\FunctionMock
 */
class ParameterBuilder
{
    /**
     * @var string The signature's parameters.
     */
    private $signatureParameters;

    /**
     * @var string The body's parameter access list.
     */
    private $bodyParameters;

    /**
     * Builds the parameters for an existing function.
     *
     * @param string $functionName The function name.
     */
    public function build($functionName)
    {
        if (!function_exists($functionName)) {
            return;
        }
        $function = new \ReflectionFunction($functionName);
        $signatureParameters = [];
        $bodyParameters = [];
        foreach ($function->getParameters() as $reflectionParameter) {
            if ($this->isVariadic($reflectionParameter)) {
                break;
            }
            $parameter = $reflectionParameter->isPassedByReference()
                ? "&$$reflectionParameter->name"
                : "$$reflectionParameter->name";
            
            $signatureParameter = $this->buildParameterSignature($reflectionParameter, $parameter);

            $signatureParameters[] = $signatureParameter;
            $bodyParameters[] = $parameter;
        }
        $this->signatureParameters = implode(", ", $signatureParameters);
        $this->bodyParameters = implode(", ", $bodyParameters);
    }
    
    /**
     * @param \ReflectionParameter $reflectionParameter
     * @param $parameter
     * @return string
     */
    private function buildParameterSignature(\ReflectionParameter $reflectionParameter, $parameter)
    {
        if (!$reflectionParameter->isOptional()) {
            return $parameter;
        }
        $defaultValue = 'null';
        if ($reflectionParameter->isDefaultValueAvailable()) {
            $defaultValue = $reflectionParameter->getDefaultValue();
        }
        return sprintf('%s = %s', $parameter, $defaultValue);
    }
    
    /**
     * Returns whether a parameter is variadic.
     *
     * @param \ReflectionParameter $parameter The parameter.
     *
     * @return boolean True, if the parameter is variadic.
     */
    private function isVariadic(\ReflectionParameter $parameter)
    {
        if ($parameter->name == "...") {
            // This is a variadic C-implementation before PHP-5.6.
            return true;
        }
        if (method_exists($parameter, "isVariadic")) {
            return $parameter->isVariadic();
        }
        return false;
    }
    
    /**
     * Returns the signature's parameters.
     *
     * @return string The signature's parameters.
     */
    public function getSignatureParameters()
    {
        return $this->signatureParameters;
    }
    
    /**
     * Returns the body's parameter access list.
     *
     * @return string The body's parameter list.
     */
    public function getBodyParameters()
    {
        return $this->bodyParameters;
    }
}
