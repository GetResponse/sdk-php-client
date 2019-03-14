<?php

namespace Getresponse\Sdk\Client\Operation;

/**
 * Class PaginationValues
 * @package Getresponse\Sdk\Client\Operation
 */
class PaginationValues
{
    /**
     * @var int
     */
    private $page;
    /**
     * @var int
     */
    private $totalPages;
    /**
     * @var int
     */
    private $totalCount;

    /**
     * PaginationValues constructor.
     * @param $page
     * @param $totalPages
     * @param $totalCount
     */
    public function __construct($page, $totalPages, $totalCount)
    {
        $this->page = $page;
        $this->totalPages = $totalPages;
        $this->totalCount = $totalCount;
    }

    /**
     * @return int
     */
    public function getPage()
    {
        return $this->page;
    }

    /**
     * @return int
     */
    public function getTotalPages()
    {
        return $this->totalPages;
    }

    /**
     * @return int
     */
    public function getTotalCount()
    {
        return $this->totalCount;
    }

}