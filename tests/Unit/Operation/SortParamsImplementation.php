<?php

namespace Getresponse\Sdk\Client\Test\Unit\Operation;

use Getresponse\Sdk\Client\Operation\SortParams;

/**
 * Class SortParamsImplementation
 * @package Getresponse\Sdk\Client\Test\Unit\Operation
 */
class SortParamsImplementation extends SortParams
{
    /**
     * @return array
     */
    protected function getAllowedKeys()
    {
        return ['email', 'name'];
    }
}
