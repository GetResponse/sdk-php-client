<?php
namespace Getresponse\Sdk\Client\Debugger;

/**
 * Class Formatter
 * @package Getresponse\Sdk\Client\Debugger
 */
interface Formatter
{
    /**
     * @param array $data
     * @return mixed
     */
    public function format(array $data);
}