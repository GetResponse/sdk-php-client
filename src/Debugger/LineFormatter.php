<?php
namespace Getresponse\Sdk\Client\Debugger;

/**
 * Class LineFormatter
 * @package Getresponse\Sdk\Client\Debugger
 */
class LineFormatter implements Formatter
{
    /** @var array | string[] */
    private $lines = [];
    
    /** @var int */
    private $indent = 0;
    
    /**
     * {@inheritdoc}
     */
    public function format(array $data)
    {
        $this->addHeadlineLine('CALLS');
        $lp = 1;
        foreach ($data['calls'] as $callData) {
            $this->indent = 1;
            if (!empty($callData['request'])) {
                $this->addKeyValueLine($lp++ . '. ' . $callData['request']['method'], $callData['request']['path']);
    
                $this->indent = 2;
                $this->addKeyValueLine('URL', $callData['request']['url']);
                $this->addKeyValueLine('Authorization', $callData['request']['authorization']);
                $this->addKeyValueLine('Time', $callData['datetime']);
                $this->addKeyValueLine('Protocol Version', $callData['request']['protocolVersion']);
                if (!empty($callData['response'])) {
                    $this->addKeyValueLine('Request size', $callData['metrics']['request_size']);
                    $this->addKeyValueLine('Request time', $callData['metrics']['request_time'] . ' ms');
                    $this->addKeyValueLine('Connect time', $callData['metrics']['connect_time'] . ' ms');
                }
                $this->addKeyValueLine('Request Headers', '');
                $this->indent++;
                foreach ($callData['request']['headers'] as $headerName => $headerValue) {
                    $this->addKeyValueLine($headerName, $headerValue);
                }
                $this->indent--;
                $requestBody = !empty($callData['request']['body']) ? $callData['request']['body'] : '    [EMPTY]';
                $this->addMultiLines('Request Body', $requestBody);
            } else {
                $this->addKeyValueLine($lp++ . ' Unknow request', '-');
                $this->indent = 2;
            }
            if (!empty($callData['response'])) {
                $this->addKeyValueLine('Total time', $callData['metrics']['total_time'] . ' ms');
                $this->addKeyValueLine('Size download', $callData['metrics']['size_download']);
                $this->addKeyValueLine('Speed download', $callData['metrics']['speed_download']);
                $this->addKeyValueLine('Result', $callData['response']['statusCode'] . ' ' . $callData['response']['reasonPhrase']);
                $this->addKeyValueLine('Response Headers', '');
                $this->indent++;
                foreach ($callData['response']['headers'] as $headerName => $headerValue) {
                    $this->addKeyValueLine($headerName, $headerValue);
                }
                $this->indent--;
                $responseBody = !empty($callData['response']['body']) ? $callData['response']['body'] : '    [EMPTY]';
                $this->addMultiLines('Response Body', $responseBody);
            }
        }
    
        $this->addHeadlineLine('SUMMARY');
    
        $this->indent = 1;
        $this->addKeyValueLine('Date', $data['date']);
        $this->addKeyValueLine('Total time', round($data['metrics']['total_time'] * 1000) . ' ms');
        $this->addKeyValueLine('Calls', $data['metrics']['calls']);
        $this->addKeyValueLine('Errors count', $data['metrics']['error_count']);
    
        $this->addHeadlineLine('ENV');
    
        $this->indent = 1;
        $this->addKeyValueLine('PHP Version', $data['php_version']);
        $this->addKeyValueLine('SAPI', $data['sapi']);
        $this->addKeyValueLine('xdebug', (true === $data['xdebug']) ? 'yes' : 'no');
        $this->addKeyValueLine('curl', (true === $data['curl']) ? 'yes' : 'no');
        
        return join(PHP_EOL, $this->lines);
    }
    
    /**
     * @param string $line
     */
    private function addLine($line)
    {
        $this->lines[] = sprintf("%' " . ($this->indent*4) . 's', '') . $line;
    }
    
    /**
     * @param string $headline
     */
    private function addHeadlineLine($headline)
    {
        $this->indent = 0;
        $this->lines[] = '--------- ' . $headline . ' ---------';
    }
    
    /**
     * @param string $key
     * @param string $value
     */
    private function addKeyValueLine($key, $value)
    {
        if ('' !== $value) {
            $value = ' ' . $value;
        }
        $this->addLine($key . ':' . $value);
    }
    
    /**
     * @param string $key
     * @param mixed $value
     */
    private function addMultiLines($key, $value)
    {
        if (is_array($value) && extension_loaded('json')) {
            $content = json_encode($value, JSON_PRETTY_PRINT);
        } else {
            $content = print_r($value, true);
        }
        $this->addKeyValueLine($key, '');
        foreach (explode("\n", $content) as $line) {
            $this->addLine($line);
        }
    }
}