<?php
namespace Getresponse\Sdk\Client\Debugger;

/**
 * Class Debugger
 * @package Getresponse\Sdk\Client\Debugger
 */
class Debugger
{
    /** @var DataCollector */
    private $dataCollector;
    
    /**
     * Debugger constructor.
     * @param DataCollector $dataCollector
     */
    public function __construct(DataCollector $dataCollector)
    {
        $this->dataCollector = $dataCollector;
    }
    
    /**
     * @param Formatter $formatter
     * @param DebugDumper $debugDumper
     */
    public function debug(Formatter $formatter, DebugDumper $debugDumper)
    {
        $data = $this->dataCollector->getData();
        $formattedData = $formatter->format($data);
        $debugDumper->dump($formattedData);
    }
    
    /**
     * @return void
     */
    public function dump()
    {
        (new DisplayDebugDumper())->dump((new LineFormatter())->format($this->dataCollector->getData()));
    }
    
    /**
     * @param string $filename
     * @throws \InvalidArgumentException
     */
    public function dumpToFile($filename)
    {
        (new FileDebugDumper($filename))->dump((new LineFormatter())->format($this->dataCollector->getData()));
    }
}