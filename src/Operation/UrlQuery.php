<?php

namespace Getresponse\Sdk\Client\Operation;

/**
 * Class SearchQuery
 * @package Getresponse\Sdk\Client\Operation
 */
abstract class UrlQuery
{
    private $urlQuery = [];

    /**
     * @return array
     */
    abstract protected function getAllowedKeys();

    /**
     * @param string $key
     * @param mixed $value
     * @return $this
     * @throws \InvalidArgumentException
     */
    public function set($key, $value)
    {
        $allowedKeys = $this->getAllowedKeys();

        if (!in_array($key, $allowedKeys, true)) {
            throw new \InvalidArgumentException('Invalid url query field');
        }

        $this->urlQuery[$key] = $value;

        return $this;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return $this->urlQuery;
    }
}
