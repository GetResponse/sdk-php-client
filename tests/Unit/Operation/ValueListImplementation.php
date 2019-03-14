<?php

namespace Getresponse\Sdk\Client\Test\Unit\Operation;

use Getresponse\Sdk\Client\Operation\ValueList;

/**
 * Class ValueListImplementation
 * @package Getresponse\Sdk\Client\Test\Unit\Operation
 */
class ValueListImplementation extends ValueList
{
    /**
     * @return array
     */
    protected function getAllowedValues()
    {
        return ['name', 'email', 'campaign'];
    }
}
