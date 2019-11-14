<?php

namespace Getresponse\Sdk\Client\Test\Unit\Exception;

use Getresponse\Sdk\Client\Exception\BadRequestException;
use Getresponse\Sdk\Client\Exception\ClientException;
use Getresponse\Sdk\Client\Exception\ConnectException;
use Getresponse\Sdk\Client\Exception\ExceptionFactory;
use Getresponse\Sdk\Client\Exception\ForbiddenException;
use Getresponse\Sdk\Client\Exception\RequestException;
use Getresponse\Sdk\Client\Exception\ServerException;
use Getresponse\Sdk\Client\Exception\UnauthorizedException;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;

/**
 * Class ExceptionFactoryTest
 * @package Getresponse\Sdk\Client\Test\Unit\Exception
 */
class ExceptionFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     * @param int $code
     * @param string $expectedClass
     * @param string $expectedMessage
     * @dataProvider codeProvider
     */
    public function shouldInstantiateCorrectExceptionClass($code, $expectedClass, $expectedMessage)
    {
        $request = new Request('get', 'http://url.com', []);
        if ($code < 100 || $code >= 600) {
            $response = null;
        } else {
            $response = new Response($code, []);
        }
        $message = 'error message';
        $handlerInfo = ['foo' => 'bar'];
        $exception = ExceptionFactory::exceptionFrom($code, $request, $message, $handlerInfo, '10', $response);

        static::assertEquals($expectedClass, get_class($exception));
        static::assertEquals($request, $exception->getRequest());
        static::assertEquals($response, $exception->getResponse());
        static::assertEquals($handlerInfo, $exception->getHandlerInfo());
        static::assertEquals($expectedMessage, $exception->getMessage());
        static::assertEquals('10', $exception->getClientVersion());
    }

    /**
     * @return array
     */
    public function codeProvider()
    {
        return [
            [
                500,
                ServerException::class,
                'Request error, response code: 500, ' . ServerException::SERVER_ERROR_MSG . '. error message, client version: 10'
            ],
            [
                501,
                ServerException::class,
                'Request error, response code: 501, ' . ServerException::SERVER_ERROR_MSG . '. error message, client version: 10'
            ],
            [
                502,
                ServerException::class,
                'Request error, response code: 502, ' . ServerException::SERVER_ERROR_MSG . '. error message, client version: 10'
            ],
            [
                503,
                ServerException::class,
                'Request error, response code: 503, ' . ServerException::SERVER_ERROR_MSG . '. error message, client version: 10'
            ],
            [
                499,
                ClientException::class,
                'Request error, response code: 499, ' . ClientException::CLIENT_ERROR_MSG . '. error message, client version: 10'
            ],
            [
                423,
                ClientException::class,
                'Request error, response code: 423, ' . ClientException::CLIENT_ERROR_MSG . '. error message, client version: 10'
            ],
            [
                409,
                ClientException::class,
                'Request error, response code: 409, ' . ClientException::CLIENT_ERROR_MSG . '. error message, client version: 10'
            ],
            [
                403,
                ForbiddenException::class,
                'Request error, response code: 403, ' . ForbiddenException::FORBIDDEN_ERROR_MSG . '. error message, client version: 10'
            ],
            [
                401,
                UnauthorizedException::class,
                'Request error, response code: 401, ' . UnauthorizedException::CLIENT_ERROR_UNAUTHORIZED_MSG . '. error message, client version: 10'
            ],
            [
                399,
                RequestException::class,
                'Request error, response code: 399, ' . RequestException::ERROR_MSG . '. error message, client version: 10'
            ],
            [
                400,
                BadRequestException::class,
                'Request error, response code: 400, ' . ClientException::CLIENT_ERROR_MSG . '. error message, client version: 10'
            ],
            [
                0,
                ConnectException::class,
                'Request error, response code: 0, ' . ConnectException::CONNECTION_ERROR_MSG . '. error message, client version: 10'
            ],

        ];
    }

}
