<?php

namespace Getresponse\Sdk\Client\Operation;

/**
 * Class SortParams
 * @package Getresponse\Sdk\Client\Operation
 */
abstract class SortParams
{
    const SORT_ASC = 'asc';
    const SORT_DESC = 'desc';
    private static $allowedValues = [self::SORT_ASC, self::SORT_DESC];

    /**
     * @var array
     */
    private $sortParams = [];

    abstract protected function getAllowedKeys();

    /**
     * @param string $field
     * @param string $value asc, desc
     * @return $this
     * @throws \InvalidArgumentException
     */
    public function sortBy($field, $value)
    {
        if (!in_array(strtolower($value), self::$allowedValues, true)) {
            throw new \InvalidArgumentException('Not allowed sort direction');
        }
        if (!in_array($field, $this->getAllowedKeys(), true)) {
            throw new \InvalidArgumentException('Not allowed sort param');
        }
        $this->sortParams[$field] = $value;

        return $this;
    }

    /**
     * @param string $field
     * @return $this
     * @throws \InvalidArgumentException
     */
    public function sortAscBy($field)
    {
        return $this->sortBy($field, self::SORT_ASC);
    }

    /**
     * @param $field
     * @return $this
     * @throws \InvalidArgumentException
     */
    public function sortDescBy($field)
    {
        return $this->sortBy($field, self::SORT_DESC);
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return $this->sortParams;
    }
}
