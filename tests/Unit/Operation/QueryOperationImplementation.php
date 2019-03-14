<?php

namespace Getresponse\Sdk\Client\Test\Unit\Operation;

use Getresponse\Sdk\Client\Operation\Pagination;
use Getresponse\Sdk\Client\Operation\QueryOperation;

/**
 * Class QueryOperationImplementation
 * @package Getresponse\Sdk\Client\Test\Unit\Operation
 */
class QueryOperationImplementation extends QueryOperation
{
    /**
     * @var SearchQueryImplementation
     */
    private $query;

    /**
     * @var SortParamsImplementation
     */
    private $sort;

    /**
     * @var Pagination
     */
    private $pagination;

    /**
     * @var ValueListImplementation
     */
    private $fields;

    /**
     * @return string
     */
    public function getUrl()
    {
        $extra = array_merge(
            $this->getPaginationParametersArray($this->pagination),
            $this->getFieldsParameterArray($this->fields)
        );
        $queryString = $this->buildQueryString($this->query, $this->sort, $extra);

        return '/some-url/123' . $queryString;
    }

    /**
     * @return string
     */
    public function getUrlWithEmptyDefaultValues()
    {
        $queryString = $this->buildQueryString($this->query, null, null);
        return '/some-url/123' . $queryString;
    }

    /**
     * @param SearchQueryImplementation $query
     */
    public function setQuery(SearchQueryImplementation $query)
    {
        $this->query = $query;
    }

    /**
     * @param SortParamsImplementation $sort
     */
    public function setSort(SortParamsImplementation $sort)
    {
        $this->sort = $sort;
    }

    /**
     * @param Pagination $pagination
     */
    public function setPagination(Pagination $pagination)
    {
        $this->pagination = $pagination;
    }

    /**
     * @param ValueListImplementation $fields
     */
    public function setFields(ValueListImplementation $fields)
    {
        $this->fields = $fields;
    }
    
    /**
     * @return string
     */
    public function getVersion()
    {
        return 'Operation-1.0';
    }
}
