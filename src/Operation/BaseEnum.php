<?php
namespace Getresponse\Sdk\Client\Operation;

/**
 * Class BaseEnum
 * @package Getresponse\Sdk\Client\Model
 */
abstract class BaseEnum implements \JsonSerializable
{
    /**
     * @var string|string[]
     */
    protected $value;
    
    /**
     * @var string[]
     */
    protected $allowedValues = [];
    
    /**
     * @return string[]
     */
    abstract public function getAllowedValues();
    
    /**
     * @return boolean
     */
    abstract public function isMultiple();
    
    /**
     * @param string|string[] $value
     */
    public function __construct($value)
    {
        if ($this->isMultiple()) {
            $value = (array) $value;
        }
        $this->validateValue($value);
        $this->value = $value;
    }
    
    /**
     * @param mixed $value
     * @throws \InvalidArgumentException
     */
    protected function validateValue($value)
    {
        $validateMethod = function ($value) {
            if (!in_array($value, $this->getAllowedValues(), true)) {
                throw new \InvalidArgumentException(
                    'Invalid value "'. $value .'". Allowed values are: ' . implode(',', $this->allowedValues)
                );
            }
        };
        
        if ($this->isMultiple()) {
            array_walk($value, $validateMethod);
            
        } else {
            $validateMethod($value);
        }
    }
    
    /**
     * @return string|array
     */
    public function getValue()
    {
        return $this->value;
    }
}