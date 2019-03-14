<?php

namespace Getresponse\Sdk\Client\Handler;

use Getresponse\Sdk\Client\Exception\CallLimitOutOfBoundsException;
use Getresponse\Sdk\Client\Exception\ConnectException;
use Getresponse\Sdk\Client\Exception\ExceptionFactory;
use Getresponse\Sdk\Client\Exception\RequestException;
use Getresponse\Sdk\Client\Handler\Call\CallRegistry;
use Getresponse\Sdk\Client\Version;

/**
 * Class CurlMultiRequestHandler
 * @package Getresponse\Sdk\Client\Handler
 */
class CurlMultiRequestHandler extends CurlRequestHandler
{
    const MAX_CALLS_LIMIT = 80;
    
    /**
     * @param CallRegistry $callRegistry
     * @throws CallLimitOutOfBoundsException
     */
    public function sendMany(CallRegistry $callRegistry)
    {
        if (self::MAX_CALLS_LIMIT <= count($callRegistry)) {
            throw new CallLimitOutOfBoundsException(
                'CurlMultiRequestHandler handles maximum ' . self::MAX_CALLS_LIMIT . ' calls at once. ' .
                'This limitation is caused by the API parallel request limits.' //todo.. add link to apidocs limits & throttling page
            );
        }
        
        $multiHandle = curl_multi_init();
    
        $curlHandles = [];
        foreach ($callRegistry as $call) {
            $curlHandles[$call->getIdentifier()] = $curlHandle = curl_init();
            
            $this->setUpCurl($call->getRequest(), $curlHandle);
            curl_multi_add_handle($multiHandle, $curlHandle);
        }
        $isRunning = null;
    
        do {
            curl_multi_exec($multiHandle, $isRunning);
        } while ($isRunning);
    
        foreach ($curlHandles as $callIdentifier => $handle) {
            if (!$callRegistry->has($callIdentifier)) {
                continue;
            }
            $call = $callRegistry->get($callIdentifier);
            if (curl_errno($handle) !== 0) {
                $call->setException(
                    ExceptionFactory::exceptionFrom(
                        ConnectException::CODE,
                        $call->getRequest(),
                        curl_error($handle),
                        RequestException::getHandlerInfoFromCurlHandler($handle),
                        Version::VERSION
                    )
                );
            } else {
                try {
                    $response = $this->parseResponse(curl_multi_getcontent($handle));
                    $call->setResponse($response);
                } catch (ParseResponseException $exception) {
                    $call->setException(
                        ExceptionFactory::exceptionFrom(
                            ConnectException::CODE,
                            $call->getRequest(),
                            $exception->getPrevious()->getMessage(),
                            RequestException::getHandlerInfoFromCurlHandler($handle),
                            Version::VERSION
                        )
                    );
                }
            }

            $info = CurlCallInfoFactory::createFromInfo(curl_getinfo($handle));
            $this->getLogger()->debugCall($call, $info);
        
            curl_multi_remove_handle($multiHandle, $handle);
            curl_close($handle);
        }
        curl_multi_close($multiHandle);
    }
}
