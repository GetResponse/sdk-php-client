<?php
namespace Getresponse\Sdk\Client\Exception;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * Class ExceptionFactory
 * @package Getresponse\Sdk\Client\Exception
 */
class ExceptionFactory
{
    /**
     * @param int $httpStatusCode
     * @param RequestInterface $request
     * @param $message
     * @param array $handlerInfo
     * @param string $clientVersion
     * @param ResponseInterface $response
     * @return RequestException
     */
    public static function exceptionFrom(
        $httpStatusCode,
        RequestInterface $request,
        $message,
        array $handlerInfo,
        $clientVersion,
        ResponseInterface $response = null
    ) {
        if ($httpStatusCode === ConnectException::CODE) {
            return new ConnectException($message, $request, $handlerInfo, $clientVersion, $response);
        }
        if ($httpStatusCode > 499) {
            return new ServerException($message, $request, $handlerInfo, $clientVersion, $response);
        }
        if ($httpStatusCode == 403) {
            return new ForbiddenException($message, $request, $handlerInfo, $clientVersion, $response);
        }
        if ($httpStatusCode == 401) {
            return new UnauthorizedException($message, $request, $handlerInfo, $clientVersion, $response);
        }
        if ($httpStatusCode == 400) {
            return new BadRequestException($message, $request, $handlerInfo, $clientVersion, $response);
        }
        if ($httpStatusCode > 400) {
            return new ClientException($message, $request, $handlerInfo, $clientVersion, $response);
        }

        return new RequestException($message, $request, $handlerInfo, $clientVersion, $response);
    }
}
