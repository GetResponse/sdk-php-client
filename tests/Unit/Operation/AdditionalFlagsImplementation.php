<?php

namespace Getresponse\Sdk\Client\Test\Unit\Operation;

use Getresponse\Sdk\Client\Operation\ValueList;

/**
 * Class AdditionalFlagsImplementation
 * @package Getresponse\Sdk\Client\Test\Unit\Operation
 */
class AdditionalFlagsImplementation extends ValueList
{
    /**
     * @return array
     */
    public function getAllowedValues()
    {
        return [
            'boo',
        ];
    }
}
