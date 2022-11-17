<?php

namespace Getresponse\Sdk\Client\Test\Unit\Handler\Call;

use Getresponse\Sdk\Client\Exception\RequestException;
use Getresponse\Sdk\Client\Handler\Call\Call;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Psr\Http\Message\RequestInterface;

/**
 * Class CallTest
 * @package Unit\Handler
 */
class CallTest extends TestCase
{
    use ProphecyTrait;
    /**
     * @var Call
     */
    private $systemUnderTest;
    
    protected function setUp(): void
    {
        $request = new Request(
            'POST',
            'https://api.getresponse.com/v3/contacts',
            [],
            '{"email:"test@email.com""}'
        );
        $this->systemUnderTest = new Call($request, 201, 'someId');
    }
    
    /**
     * @test
     */
    public function shouldReturnCallId()
    {
        self::assertEquals('someId', $this->systemUnderTest->getIdentifier());
    }
    
    /**
     * @test
     */
    public function shouldReturnCallExpectedSuccessCode()
    {
        self::assertEquals(201, $this->systemUnderTest->getSuccessCode());
    }
    
    /**
     * @test
     */
    public function shouldReturnCallRequest()
    {
        self::assertInstanceOf(RequestInterface::class, $this->systemUnderTest->getRequest());
    }
    
    /**
     * @test
     */
    public function shouldAssumeCallResponse()
    {
        $response = new Response(201, [], '{"contactId":"one","email":"test@email.com"}');
        $this->systemUnderTest->setResponse($response);
        self::assertEquals($response, $this->systemUnderTest->getResponse());
    }
    
    /**
     * @test
     */
    public function shouldAssumeExceptionThrowDuringCall()
    {
        $exceptionMock = $this->prophesize(RequestException::class)->reveal();
        $this->systemUnderTest->setException($exceptionMock);
        self::assertTrue($this->systemUnderTest->hasException());
        self::assertEquals($exceptionMock, $this->systemUnderTest->getException());
    }
    
    /**
     * @test
     */
    public function shouldThrowExceptionWhenSetResponseAfterException()
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Invalid state. Call has an exception within.');
        $exceptionMock = $this->prophesize(RequestException::class)->reveal();
        $this->systemUnderTest->setException($exceptionMock);
    
        $response = new Response(201, [], '{"contactId":"one","email":"test@email.com"}');
        $this->systemUnderTest->setResponse($response);
    }
    
    /**
     * @test
     */
    public function shouldThrowExceptionWhenOverwriteException()
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Exception cannot be overwritten.');
        $this->systemUnderTest->setException($this->prophesize(RequestException::class)->reveal());
        $this->systemUnderTest->setException($this->prophesize(RequestException::class)->reveal());
    }
    
    /**
     * @test
     */
    public function shouldThrowExceptionWhenSetExceptionAfterResponse()
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Invalid state. Call has a response within.');
        $response = new Response(201, [], '{"contactId":"one","email":"test@email.com"}');
        $this->systemUnderTest->setResponse($response);
    
        $exceptionMock = $this->prophesize(RequestException::class)->reveal();
        $this->systemUnderTest->setException($exceptionMock);
    }
    
    /**
     * @test
     */
    public function shouldThrowExceptionWhenOverwriteResponse()
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Response cannot be overwritten.');
        $this->systemUnderTest->setResponse(new Response(201, [], '{"contactId":"one","email":"test1@email.com"}'));
        $this->systemUnderTest->setResponse(new Response(201, [], '{"contactId":"one","email":"test2@email.com"}'));
    }
    
    /**
     * @test
     */
    public function shouldCheckIsCallSucceededWithExpectedSuccessCodeInResponse()
    {
        self::assertFalse($this->systemUnderTest->isSucceeded());
        
        $response = new Response(201, [], '{"contactId":"one","email":"test@email.com"}');
        $this->systemUnderTest->setResponse($response);
        self::assertTrue($this->systemUnderTest->isSucceeded());
    }
    
    /**
     * @test
     */
    public function shouldCheckIsCallFinishedWithResponseWithin()
    {
        self::assertFalse($this->systemUnderTest->isFinished());
    
        $response = new Response(201, [], '{"contactId":"one","email":"test@email.com"}');
        $this->systemUnderTest->setResponse($response);
        self::assertTrue($this->systemUnderTest->isFinished());
    }
    
    /**
     * @test
     */
    public function shouldCheckIsCallFinishedWithExceptionWithin()
    {
        self::assertFalse($this->systemUnderTest->isFinished());
    
        $exceptionMock = $this->prophesize(RequestException::class)->reveal();
        $this->systemUnderTest->setException($exceptionMock);
        self::assertTrue($this->systemUnderTest->isFinished());
    }
}
