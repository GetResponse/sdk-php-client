<?php

namespace Getresponse\Sdk\Client\Test\Unit\Operation;

use Getresponse\Sdk\Client\Operation\UrlQuery;

/**
 * Class UrlQueryParametersImplementation
 * @package Getresponse\Sdk\Client\Test\Unit\Operation
 */
class UrlQueryParametersImplementation extends UrlQuery
{
    /**
     * @return array
     */
    public function getAllowedKeys()
    {
        return [
            'foo',
        ];
    }
    
    
    /**
     * @param string $foo
     * @return $this
     */
    public function whereFoo($foo)
    {
        return $this->set('foo', $foo);
    }
}
