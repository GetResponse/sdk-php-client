<?php

namespace Getresponse\Sdk\Client\Operation;

/**
 * Abstract class used as a base for comma separated lists, like "fields" or "additionalFlags".
 *
 * Class ValueList
 * @package Getresponse\Sdk\Client\Operation
 */
abstract class ValueList
{
    /**
     * @var string[]
     */
    protected $values = [];

    /**
     * @return array
     */
    abstract protected function getAllowedValues();

    /**
     * Fields constructor.
     * @param $value, $value
     * @throws \InvalidArgumentException
     */
    public function __construct()
    {
        $values = func_get_args();
        $invalidFields = array_diff($values, $this->getAllowedValues());
        if (!empty($invalidFields)) {
            throw new \InvalidArgumentException('Invalid values: ' . implode(', ', $invalidFields));
        }
        $this->values = $values;
    }

    /**
     * @return string
     */
    public function toString()
    {
        return implode(',', $this->values);
    }
}
