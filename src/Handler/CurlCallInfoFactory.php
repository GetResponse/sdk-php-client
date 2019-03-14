<?php
namespace Getresponse\Sdk\Client\Handler;

use Getresponse\Sdk\Client\Handler\Call\CallInfo;

/**
 * Class CurlCallInfoFactory
 * @package Getresponse\Sdk\Client\Handler
 */
class CurlCallInfoFactory
{
    /**
     * @param array | bool $curlInfo
     * @return CallInfo | null
     */
    public static function createFromInfo($curlInfo)
    {
        if (!is_array($curlInfo)) {
            return null;
        }
        
        return (new CallInfo())
            ->withConnectTime($curlInfo['connect_time'])
            ->withRequestSize($curlInfo['request_size'])
            ->withSizeDownload($curlInfo['size_download'])
            ->withSpeedDownload($curlInfo['speed_download'])
            ->withTotalTime($curlInfo['total_time']);
    }
}