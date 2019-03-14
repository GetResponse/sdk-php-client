<?php
namespace Getresponse\Sdk\Client\Debugger;

/**
 * Class DisplayDebugDumper
 * @package Getresponse\Sdk\Client\Debugger
 */
class DisplayDebugDumper implements DebugDumper
{
    /**
     * {@inheritdoc}
     */
    public function dump($debug)
    {
        echo $debug . PHP_EOL;
    }
}