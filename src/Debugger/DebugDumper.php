<?php
namespace Getresponse\Sdk\Client\Debugger;

/**
 * Class DebugDumper
 * @package Getresponse\Sdk\Client\Debugger
 */
interface DebugDumper
{
    /**
     * @param mixed $debug
     * @return void
     */
    public function dump($debug);
}