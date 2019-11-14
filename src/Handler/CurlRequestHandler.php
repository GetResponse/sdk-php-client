<?php

namespace Getresponse\Sdk\Client\Handler;

use Getresponse\Sdk\Client\Debugger\Logger;
use Getresponse\Sdk\Client\Exception\ConnectException;
use Getresponse\Sdk\Client\Exception\ExceptionFactory;
use Getresponse\Sdk\Client\Exception\RequestException;
use Getresponse\Sdk\Client\Handler\Call\Call;
use Getresponse\Sdk\Client\Handler\Call\CallRegistry;
use Getresponse\Sdk\Client\Version;
use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\RequestInterface;
use Psr\Log\NullLogger;

/**
 * Class CurlRequestHandler
 * @package Getresponse\Sdk\Client\Handler
 */
class CurlRequestHandler implements RequestHandler
{
    const METHOD_POST = 'POST';
    const METHOD_DELETE = 'DELETE';

    /**
     * @var Logger
     */
    private $logger;
    
    /**
     * CurlRequestHandler constructor.
     */
    public function __construct()
    {
        $this->logger = new Logger(new NullLogger());
    }
    
    /**
     * @param Logger $logger
     */
    public function setLogger(Logger $logger)
    {
        $this->logger = $logger;
    }
    
    /**
     * @return Logger
     */
    protected function getLogger()
    {
        return $this->logger;
    }
    
    /**
     * @param Call $call
     */
    public function send(Call $call)
    {
        $curlHandle = curl_init();

        try {
            $response = $this->parseResponse($this->sendCurlRequest($call->getRequest(), $curlHandle));
            $call->setResponse($response);
        } catch (ParseResponseException $e) {
            $call->setException(
                ExceptionFactory::exceptionFrom(
                    ConnectException::CODE,
                    $call->getRequest(),
                    $e->getPrevious()->getMessage(),
                    RequestException::getHandlerInfoFromCurlHandler($curlHandle),
                    Version::VERSION
                )
            );
        } catch (RequestException $e) {
            $call->setException($e);
        }
    
        $info = CurlCallInfoFactory::createFromInfo(curl_getinfo($curlHandle));
        curl_close($curlHandle);

        $this->getLogger()->debugCall($call, $info);
    }

    /**
     * @param CallRegistry $callRegistry
     */
    public function sendMany(CallRegistry $callRegistry)
    {
        $curlHandle = curl_init();

        foreach ($callRegistry as $call) {
            try {
                $response = $this->parseResponse($this->sendCurlRequest($call->getRequest(), $curlHandle));
                $call->setResponse($response);
            } catch (ParseResponseException $e) {
                $call->setException(
                    ExceptionFactory::exceptionFrom(
                        ConnectException::CODE,
                        $call->getRequest(),
                        $e->getPrevious()->getMessage(),
                        RequestException::getHandlerInfoFromCurlHandler($curlHandle),
                        Version::VERSION
                    )
                );
            } catch (RequestException $e) {
                $call->setException($e);
            }
    
            $info = CurlCallInfoFactory::createFromInfo(curl_getinfo($curlHandle));
            $this->getLogger()->debugCall($call, $info);
        }

        curl_close($curlHandle);
    }
    
    /**
     * @return string
     */
    public function getUAString()
    {
        $version = curl_version();
        $curlVersion = (!empty($version['version'])) ? $version['version'] : '-';
        return sprintf('cURL %s; PHP %s; %s; %s', $curlVersion, PHP_VERSION, PHP_OS, PHP_SAPI);
    }
    
    /**
     * @param RequestInterface $request
     * @param $curlHandle
     * @return string
     * @throws RequestException
     */
    private function sendCurlRequest(RequestInterface $request, $curlHandle)
    {
        $this->setUpCurl($request, $curlHandle);

        $response = curl_exec($curlHandle);

        if (curl_errno($curlHandle) !== 0) {
            throw ExceptionFactory::exceptionFrom(
                ConnectException::CODE,
                $request,
                curl_error($curlHandle),
                RequestException::getHandlerInfoFromCurlHandler($curlHandle),
                Version::VERSION
            );
        }

        return $response;
    }

    /**
     * @param RequestInterface $request
     * @param $curlHandle
     */
    protected function setUpCurl(RequestInterface $request, $curlHandle)
    {
        $this->getLogger()->debugRequest($request);

        curl_setopt($curlHandle, CURLOPT_URL, $request->getUri());
        curl_setopt($curlHandle, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curlHandle, CURLOPT_HEADER, true);

        $headers = $request->getHeaders();

        if ($request->getMethod() === self::METHOD_POST) {
            $headers['Content-type'] = 'application/json';
            curl_setopt($curlHandle, CURLOPT_POST, true);
            curl_setopt($curlHandle, CURLOPT_POSTFIELDS, (string) $request->getBody());
        }

        if (!$request->hasHeader('expect') && $request->getBody()->getSize() > 0) {
            // prevent cURL from adding `Expect: 100-continue` automatically
            $headers['Expect'] = '';
        }

        if ($request->getMethod() === self::METHOD_DELETE) {
            curl_setopt($curlHandle, CURLOPT_CUSTOMREQUEST, self::METHOD_DELETE);
            if ($request->getBody()->getSize() > 0) {
                $headers['Content-type'] = 'application/json';
                curl_setopt($curlHandle, CURLOPT_POSTFIELDS, $request->getBody());
            }
        }

        $this->setUpHeaders($curlHandle, $headers);
    }

    /**
     * @param string $message
     * @return Response
     * @throws ParseResponseException
     */
    protected function parseResponse($message)
    {
        try {
            return \GuzzleHttp\Psr7\parse_response($message);
        } catch (\InvalidArgumentException $e) {
            throw ParseResponseException::create($e);
        }
    }

    /**
     * @param $curlHandle
     * @param $headers
     */
    private function setUpHeaders($curlHandle, $headers)
    {
        curl_setopt($curlHandle, CURLOPT_HTTPHEADER, array_map(function ($name, $value) {
            if (is_array($value)) {
                return $name . ': ' . implode(',', $value);
            }

            return $name . ': ' . $value;
        }, array_keys($headers), $headers));
    }
}
