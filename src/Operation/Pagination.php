<?php

namespace Getresponse\Sdk\Client\Operation;

/**
 * Class Pagination
 * @package Getresponse\Sdk\Client\Operation
 */
class Pagination
{
    /**
     * @var int
     */
    private $page;

    /**
     * @var int
     */
    private $perPage;

    /**
     * Pagination constructor.
     * @param int $page
     * @param int $perPage
     */
    public function __construct($page, $perPage)
    {
        $this->page = $page;
        $this->perPage = $perPage;
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
    public function getPerPage()
    {
        return $this->perPage;
    }
}
