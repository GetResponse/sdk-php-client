<?php

namespace Getresponse\Sdk\Client\Test\Unit\Operation;

use Getresponse\Sdk\Client\Operation\SearchQuery;

/**
 * Class SearchQueryImplementation
 * @package Getresponse\Sdk\Client\Test\Unit\Operation
 */
class SearchQueryImplementation extends SearchQuery
{
    /**
     * @return array
     */
    protected function getAllowedKeys()
    {
        return ['email', 'name'];
    }
}
