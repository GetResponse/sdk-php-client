<?php

namespace Getresponse\Sdk\Client\Environment;

use Psr\Http\Message\RequestInterface;

/**
 * Interface Environment
 * @package Getresponse\Sdk\Client\Environment
 */
interface Environment
{
    /**
     * @return string
     */
    public function getUrl();

    /**
     * @param RequestInterface $request
     * @return RequestInterface
     */
    public function processRequest(RequestInterface $request);
}
