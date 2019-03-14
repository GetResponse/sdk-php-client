<?php
namespace Getresponse\Sdk\Client\Handler\Call;

/**
 * Class CallInfo
 * @package Getresponse\Sdk\Client\Handler\Call
 */
class CallInfo
{
    /** @var int */
    private $requestSize;
    
    /** @var float */
    private $connectTime;
    
    /** @var float */
    private $totalTime;
    
    /** @var int */
    private $sizeDownload;
    
    /** @var int */
    private $speedDownload;
    
    /**
     * @return int
     */
    public function getRequestSize()
    {
        return $this->requestSize;
    }
    
    /**
     * @param int $requestSize
     * @return CallInfo
     */
    public function withRequestSize($requestSize)
    {
        $o = clone $this;
        $o->requestSize = $requestSize;
        return $o;
    }
    
    /**
     * @return float
     */
    public function getConnectTime()
    {
        return $this->connectTime;
    }
    
    /**
     * @param float $connectTime
     * @return CallInfo
     */
    public function withConnectTime($connectTime)
    {
        $o = clone $this;
        $o->connectTime = $connectTime;
        return $o;
    }
    
    /**
     * @return float
     */
    public function getTotalTime()
    {
        return $this->totalTime;
    }
    
    /**
     * @param float $totalTime
     * @return CallInfo
     */
    public function withTotalTime($totalTime)
    {
        $o = clone $this;
        $o->totalTime = $totalTime;
        return $o;
    }
    
    /**
     * @return int
     */
    public function getSizeDownload()
    {
        return $this->sizeDownload;
    }
    
    /**
     * @param int $sizeDownload
     * @return CallInfo
     */
    public function withSizeDownload($sizeDownload)
    {
        $o = clone $this;
        $o->sizeDownload = $sizeDownload;
        return $o;
    }
    
    /**
     * @return int
     */
    public function getSpeedDownload()
    {
        return $this->speedDownload;
    }
    
    /**
     * @param int $speedDownload
     * @return CallInfo
     */
    public function withSpeedDownload($speedDownload)
    {
        $o = clone $this;
        $o->speedDownload = $speedDownload;
        return $o;
    }
}