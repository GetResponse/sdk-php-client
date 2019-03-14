<?php

namespace Getresponse\Sdk\Client\Operation;

/**
 * Class QueryOperation
 * @package Getresponse\Sdk\Client\Operation
 */
abstract class QueryOperation implements Operation
{
    /**
     * @param SearchQuery | null $query
     * @param SortParams | null $sort
     * @param array $extra
     * @return string
     */
    protected function buildQueryString(SearchQuery $query = null, SortParams $sort = null, array $extra = null)
    {
        if ($query === null) {
            $queryArray = [];
        } else {
            $queryArray = $query->toArray();
        }
        if ($sort === null) {
            $sortArray = [];
        } else {
            $sortArray = $sort->toArray();
        }

        if (null === $extra) {
            $extra = [];
        }

        $params = array_merge([
            'query' => $queryArray,
            'sort' => $sortArray
        ], $extra);

        $queryString = http_build_query($params);

        if (!empty($queryString)) {
            return '?' . $queryString;
        }
        return '';
    }

    /**
     * @param Pagination | null $pagination
     * @return array
     */
    protected function getPaginationParametersArray(Pagination $pagination = null)
    {
        if ($pagination !== null) {
            return [
                'page' => $pagination->getPage(),
                'perPage' => $pagination->getPerPage()
            ];
        }
        return [];
    }

    /**
     * @param ValueList | null $fields
     * @return array
     */
    protected function getFieldsParameterArray(ValueList $fields = null)
    {
        if ($fields !== null) {
            return [
                'fields' => $fields->toString()
            ];
        }
        return [];
    }

    /**
     * @param ValueList | null $additionalFlags
     * @return array
     */
    protected function getAdditionalFlagsParameterArray(ValueList $additionalFlags = null)
    {
        if ($additionalFlags !== null) {
            return [
                'additionalFlags' => $additionalFlags->toString()
            ];
        }
        return [];
    }

    /**
     * @return string
     */
    final public function getBody()
    {
        return '';
    }

    /**
     * @return string
     */
    final public function getMethod()
    {
        return Operation::GET;
    }

    /**
     * @return int
     */
    final public function getSuccessCode()
    {
        return 200;
    }
}
