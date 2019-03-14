<?php
namespace Getresponse\Sdk\Client\Operation;

/**
 * Class QueryEnum
 * @package Getresponse\Sdk\Client\Model
 */
class QueryEnum
{
    const TYPE_STRING = 'string';
    const TYPE_ARRAY = 'array';
    
    /**
     * @var string|array|null
     */
    private $value;
    
    /** 
     * @var string 
     */
    private $type;
    
    /**
     * @var string[]
     */
    private $allowedValues = [];
    
    /**
     * QueryEnum constructor.
     * @param string $type
     * @param string[] $allowedValues
     * @param string|array $value
     */
    public function __construct($type, array $allowedValues, $value)
    {
        $allowedTypes = [self::TYPE_ARRAY, self::TYPE_STRING, true];
        if (!in_array($type, $allowedTypes, true)) {
            throw new \InvalidArgumentException(
                'Invalid value in first argument of QueryEnum. Allowed values are: ' . implode(',', $allowedTypes)
            );
        }
        $this->type = $type;
        $this->allowedValues = $allowedValues;
    
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
            if (!in_array($value, $this->allowedValues, true)) {
                throw new \InvalidArgumentException(
                    'Invalid value. Allowed values are: ' . implode(',', $this->allowedValues)
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
     * @return bool
     */
    public function isMultiple()
    {
        return self::TYPE_ARRAY === $this->type;
    }
    
    /**
     * @return string|array
     */
    public function getValue()
    {
        return $this->value;
    }
}