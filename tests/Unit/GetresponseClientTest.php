<?php

namespace Getresponse\Sdk\Client\Test\Unit;

use Getresponse\Sdk\Client\Authentication\AuthenticationProvider;
use Getresponse\Sdk\Client\Debugger\Logger;
use Getresponse\Sdk\Client\Environment\Environment;
use Getresponse\Sdk\Client\GetresponseClient;
use Getresponse\Sdk\Client\Handler\Call\Call;
use Getresponse\Sdk\Client\Handler\Call\CallRegistry;
use Getresponse\Sdk\Client\Handler\RequestHandler;
use Getresponse\Sdk\Client\Operation\FailedOperationResponse;
use Getresponse\Sdk\Client\Operation\OperationResponse;
use Getresponse\Sdk\Client\Operation\Pagination;
use Getresponse\Sdk\Client\Test\Unit\Operation\QueryOperationImplementation;
use Getresponse\Sdk\Client\Version;
use GuzzleHttp\Psr7\Response;
use Prophecy\Argument;
use Prophecy\Prophecy\ObjectProphecy;
use Psr\Http\Message\RequestInterface;

/**
 * Class GetresponseClientTest
 * @package Getresponse\Sdk\Client\Test\Unit
 */
class GetresponseClientTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var GetresponseClient
     */
    private $systemUnderTest;

    /**
     * @var RequestHandler | ObjectProphecy
     */
    private $requestHandlerMock;
    
    /**
     * @var QueryOperationImplementation
     */
    private $operation;

    protected function setUp()
    {
        $this->requestHandlerMock = $this->prophesize(RequestHandler::class);
        
        $this->operation = new QueryOperationImplementation();
        
        $environmentMock = $this->prophesize(Environment::class);
        $environmentMock
            ->getUrl()
            ->willReturn('https://api.getresponse.com');

        $environmentMock
            ->processRequest(Argument::type(RequestInterface::class))
            ->will(function ($args) {
                /** @var RequestInterface $request */
                $request = $args[0];
                return $request;
            });

        $authenticationProviderMock = $this->prophesize(AuthenticationProvider::class);
        $authenticationProviderMock
            ->authenticate(Argument::type(RequestInterface::class))
            ->will(function ($args) {
                /** @var RequestInterface $request */
                $request = $args[0];
                return $request->withHeader('X-Auth', 'token');
            });
        
        $this->systemUnderTest = new GetresponseClient(
            $this->requestHandlerMock->reveal(),
            $environmentMock->reveal(),
            $authenticationProviderMock->reveal()
        );
    }

    /**
     * @test
     */
    public function shouldSendRequestUsingRequestHandler()
    {
        $this->requestHandlerMock->getUAString()->willReturn('UAStringRequestHandler')->shouldBeCalled();
        
        $this->requestHandlerMock->send(Argument::that(function (Call $call) {
            self::assertEquals('https://api.getresponse.com/some-url/123', (string) $call->getRequest()->getUri());
            self::assertEquals(['token'], $call->getRequest()->getHeader('x-auth'));
            self::assertRegExp(
                sprintf(
                    '/^%s\sGetResponse-Client\/%s\s\w+\s\(.[^\)]+\)$/',
                    str_replace('.', '\.', $this->operation->getVersion()),
                    str_replace('.', '\.', Version::VERSION)
                ),
                $call->getRequest()->getHeaderLine('user-agent')
            );
            self::assertEmpty((string) $call->getRequest()->getBody());
            return true;
        }))->shouldBeCalled()->willReturn(new Response());

        $this->systemUnderTest->call($this->operation);
    }

    /**
     * @test
     */
    public function shouldReturnFailedOperationWhenResponseCodeGreaterThan399()
    {
        $this->requestHandlerMock->getUAString()->shouldBeCalled();
        
        $this->requestHandlerMock
            ->send(Argument::type(Call::class))
            ->will(function ($args) {
                $args[0]->setResponse(new Response(400));
            })
            ->shouldBeCalled();

        $operationResponse = $this->systemUnderTest->call($this->operation);
        self::assertInstanceOf(FailedOperationResponse::class, $operationResponse);
        self::assertFalse($operationResponse->isSuccess());
        
        self::assertEquals('Operation failed', $operationResponse->getErrorMessage());
    }

    /**
     * @test
     */
    public function shouldReturnFailedOperationWhenResponseCodeGreaterThan399WithMessageFromErrorResponse()
    {
        $this->requestHandlerMock->getUAString()->shouldBeCalled();
        
        $this->requestHandlerMock
            ->send(Argument::type(Call::class))
            ->will(function ($args) {
                $args[0]->setResponse(new Response(400, [], '{"message": "Cannot add contact that is blacklisted"}'));
            })
            ->shouldBeCalled();
    
        $operationResponse = $this->systemUnderTest->call($this->operation);
        self::assertInstanceOf(FailedOperationResponse::class, $operationResponse);
        self::assertFalse($operationResponse->isSuccess());
    
        self::assertEquals('Cannot add contact that is blacklisted', $operationResponse->getErrorMessage());
    }

    /**
     * @test
     */
    public function shouldPassLoggerToRequestHandlerDuringSetLogger()
    {
        $logger = $this->prophesize(Logger::class)->reveal();

        $this->requestHandlerMock
            ->setLogger($logger)
            ->shouldBeCalled();

        $this->systemUnderTest->setLogger($logger);
    }


    /**
     * @test
     */
    public function shouldSendAsyncRequests()
    {
        $operations
            = $requestHandlerSendAsserts
            = [];
    
        $responses[1] = new Response(200, ['Content-type' => 'application/json'], '[{"contactId":"a"},{"contactId":"b"}]');
        $responses[2] = new Response(200, ['Content-type' => 'application/json'], '[{"contactId":"c"},{"contactId":"d"}]');
        $responses[3] = new Response(400, ['Content-type' => 'application/json'], '{"httpStatus": "400"}');
        $responses[4] = new Response(200, ['Content-type' => 'application/json'], '[{"contactId":"e"},{"contactId":"f"}]');
        
        for ($pageNo = 1; $pageNo <= 4; $pageNo++) {
            $pagination = new Pagination($pageNo, 1);
            $operation = new QueryOperationImplementation();
            $operation->setPagination($pagination);
            $operations[$pageNo] = $operation;
            
            $host = 'https://api.getresponse.com';
            $requestHandlerSendAsserts[$pageNo] = function (RequestInterface $request) use ($host, $pageNo) {
                self::assertEquals($host . '/some-url/123?page=' . $pageNo . '&perPage=1', (string) $request->getUri());
                self::assertEquals(['token'], $request->getHeader('x-auth'));
                self::assertEmpty((string) $request->getBody());
            };
    
            $response = $responses[$pageNo];
            $responseHandlerSendAsserts[$pageNo] = function (OperationResponse $operationResponse) use ($response) {
                self::assertEquals($response, $operationResponse->getResponse());
                self::assertEquals($response->getStatusCode(), $operationResponse->getResponse()->getStatusCode());
                self::assertEquals($response->getReasonPhrase(), $operationResponse->getResponse()->getReasonPhrase());
            };
        }
    
        $this->requestHandlerMock
            ->sendMany(Argument::type(CallRegistry::class))
            ->will(function ($args) use ($requestHandlerSendAsserts, $responses) {
                $pageNo = 1;
                foreach ($args[0] as $call) {
                    call_user_func_array($requestHandlerSendAsserts[$pageNo], [$call->getRequest()]);
                    $call->setResponse($responses[$pageNo]);
                    $pageNo++;
                };
            })
            ->shouldBeCalled();
    
        $this->requestHandlerMock->getUAString()->shouldBeCalled();
        
        $operationResponses = $this->systemUnderTest->callMany($operations);
        $pageNo = 1;
        foreach ($operationResponses as $operationResponse) {
            call_user_func_array($responseHandlerSendAsserts[$pageNo], [$operationResponse]);
            $pageNo++;
        }
    
        self::assertCount(3, $operationResponses->getSucceededOperations());
        self::assertCount(1, $operationResponses->getFailedOperations());
    }
}
