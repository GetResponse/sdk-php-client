<?php
namespace Getresponse\Sdk\Client\Debugger;

use Getresponse\Sdk\Client\Handler\Call\CallInfo;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * Class DataCollector
 * @package Getresponse\Sdk\Client\Debugger
 */
class DataCollector
{
    /** @var StreamReader */
    private $streamReader;
    
    /** @var array */
    private $calls = [];
    
    /** @var array */
    private $metrics = [
        'calls' => 0,
        'operations' => [],
        'error_count' => 0,
        'total_time' => 0
    ];
    
    /**
     * DataCollector constructor.
     * @param $streamReader $streamReader
     */
    public function __construct(StreamReader $streamReader = null)
    {
        if (null === $streamReader) {
            $streamReader = new JsonStreamReader();
        }
        $this->streamReader = $streamReader;
    }
    
    /**
     * @param RequestInterface $request
     */
    public function collectRequest(RequestInterface $request)
    {
        $this->metrics['calls']++;
    
        $hash = spl_object_hash($request);
        $this->metrics['operations'][$hash] = $request->getRequestTarget();
        $this->calls[$hash] = [
            'datetime' => @date('H:i:s'),
            'request' => $this->formatRequest($request)
        ];
    }
    
    /**
     * @param ResponseInterface $response
     * @param RequestInterface | null $request
     * @param CallInfo | null $info
     */
    public function collectResponse(
        ResponseInterface $response,
        RequestInterface $request = null,
        CallInfo $info = null
    ) {
        if ($response->getStatusCode() >= 400) {
            $this->metrics['error_count']++;
        }
        if (null !== $info) {
            $this->metrics['total_time'] += $info->getTotalTime();
        }
        
        if (null !== $request) {
            $hash = spl_object_hash($request);
            if (!isset($this->calls[$hash])) {
                $this->collectRequest($request);
            }
        } else {
            $hash = uniqid();
            $this->calls[$hash]['request'] = null;
        }
        
        $this->calls[$hash] += [
            'response' => $this->formatResponse($response),
            'metrics' => $this->formatMetrics($info),
        ];
    }
    
    /**
     * @return array
     */
    public function getData()
    {
        $loadedExtensions = get_loaded_extensions();
        return [
            'date' => @date('r'),
            'php_version' => PHP_VERSION,
            'sapi' => PHP_SAPI,
            'xdebug' => in_array('xdebug', $loadedExtensions),
            'curl' => in_array('curl', $loadedExtensions),
            'metrics' => $this->metrics,
            'calls' => $this->calls
        ];
    }
    
    /**
     * @param RequestInterface $request
     * @return array
     */
    private function formatRequest(RequestInterface $request)
    {
        $headers = [];
        $authorization = '';
        foreach ($request->getHeaders() as $name => $value) {
            $headerName = strtolower($name);
            $headers[$headerName] = $request->getHeaderLine($name);
            if (in_array($headerName, ['x-auth-token', 'authorization'], true)) {
                $authorization = $request->getHeaderLine($name);
            }
        }
    
        $result = [
            'method' => $request->getMethod(),
            'url' => $request->getUri()->getScheme() . '://'. $request->getUri()->getAuthority(),
            'path' => urldecode($request->getRequestTarget()),
            'authorization' => $authorization,
            'protocolVersion' => $request->getProtocolVersion(),
            'headers' => $headers,
            'body' => '',
        ];
        
        try {
            $result['body'] = $this->streamReader->read($request->getBody());
        } catch (StreamReaderException $responseReaderException) {
            $result['parse_errors'][] = $responseReaderException->getMessage();
        }
        return $result;
    }
    
    /**
     * @param ResponseInterface $response
     * @return array
     */
    private function formatResponse(ResponseInterface $response)
    {
        $headers = [];
        foreach ($response->getHeaders() as $name => $value) {
            $headers[$name] = $response->getHeaderLine($name);
        }
        
        $result = [
            'statusCode' => $response->getStatusCode(),
            'reasonPhrase' => $response->getReasonPhrase(),
            'headers' => $headers,
            'body' => '',
        ];
        try {
            $result['body'] = $this->streamReader->read($response->getBody());
        } catch (StreamReaderException $responseReaderException) {
            $result['parse_errors'][] = $responseReaderException->getMessage();
        }
        return $result;
    }
    
    /**
     * @param CallInfo | null $info
     * @return array
     */
    private function formatMetrics($info)
    {
        $metrics = [
            'request_size' => '-',
            'connect_time' => '-',
            'request_time' => '-',
            'total_time' => '-',
            'size_download' => '-',
            'speed_download' => '-',
        ];
        if (null !== $info) {
            $metrics['request_size'] = $info->getRequestSize();
            $metrics['connect_time'] = ceil($info->getConnectTime() * 1000);
            $metrics['request_time'] = ceil(($info->getTotalTime() - $info->getConnectTime()) * 1000);
            $metrics['total_time'] = ceil($info->getTotalTime() * 1000);
            $metrics['size_download'] = $info->getSizeDownload();
            $metrics['speed_download'] = $info->getSpeedDownload();
        }
        return $metrics;
    }
}