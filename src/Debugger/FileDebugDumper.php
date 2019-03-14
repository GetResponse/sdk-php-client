<?php
namespace Getresponse\Sdk\Client\Debugger;

use InvalidArgumentException;

/**
 * Class FileDebugDumper
 * @package Getresponse\Sdk\Client\Debugger
 */
class FileDebugDumper implements DebugDumper
{
    /** @var string */
    private $filename;
    
    /**
     * FileDebugDumper constructor.
     * @param string $filename
     * @throws InvalidArgumentException
     */
    public function __construct($filename)
    {
        $dir = pathinfo($filename, PATHINFO_DIRNAME);
        if (!is_dir($dir) || ('.' === $dir && 0 !== strpos($filename, '.'))) {
            throw new InvalidArgumentException('Invalid filename');
        }
        $this->filename = $filename;
    }
    
    /**
     * {@inheritdoc}
     */
    public function dump($debug)
    {
        file_put_contents($this->filename, $debug);
    }
}