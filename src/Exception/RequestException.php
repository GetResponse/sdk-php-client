<?php
namespace Getresponse\Sdk\Client\Exception;

use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * Class RequestException
 * @package Getresponse\Sdk\Client\Exception
 */
class RequestException extends BaseException
{
    const ERROR_CODE = 1;
    const ERROR_MSG = 'general error: see response body for details';

    /**
     * @var Request
     */
    private $request;

    /**
     * @var Response
     */
    private $response;

    /**
     * @var array
     */
    private $handlerInfo = [];

    /**
     * @var string
     */
    private $clientVersion;

    /**
     * RequestException constructor.
     *
     * @param string $message
     * @param RequestInterface $request
     * @param array $handlerInfo
     * @param string $clientVersion
     * @param ResponseInterface | null $response
     */
    public function __construct(
        $message,
        RequestInterface $request,
        array $handlerInfo,
        $clientVersion,
        ResponseInterface $response = null
    ) {
        $this->request = $request;
        $this->response = $response;
        $this->handlerInfo = $handlerInfo;
        $this->clientVersion = $clientVersion;
        parent::__construct(
            $this->getBaseMessage($response) . $message . ', client version: ' . $clientVersion,
            self::ERROR_CODE
        );
    }

    /**
     * @param ResponseInterface | null $response
     * @return string
     */
    public function getBaseMessage(ResponseInterface $response = null)
    {
        $responseCode = $response !== null ? $response->getStatusCode() : 0;
        return 'Request error, response code: ' . $responseCode . ', ' . $this->getExceptionSpecificMessage() . '. ';
    }

    /**
     * @return Request
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * @return Response | null
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * @return array
     */
    public function getHandlerInfo()
    {
        return $this->handlerInfo;
    }

    /**
     * @param resource $curlHandle
     * @return array
     */
    public static function getHandlerInfoFromCurlHandler($curlHandle)
    {
        $info = curl_getinfo($curlHandle);
        $error = curl_error($curlHandle);

        return [
            'info' => $info,
            'error' => $error
        ];
    }

    /**
     * @return string
     */
    protected function getExceptionSpecificMessage()
    {
        return self::ERROR_MSG;
    }

    /**
     * @return string
     */
    public function getClientVersion()
    {
        return $this->clientVersion;
    }

    /**
     * @return string
     */
    public function getResponseBody()
    {
        if ($this->response !== null) {
            return (string) $this->response->getBody();
        }
        return '';
    }
}
