<?php

namespace Getresponse\Sdk\Client\Test\Unit\Handler\Call;

use Getresponse\Sdk\Client\Exception\RequestException;
use Getresponse\Sdk\Client\Handler\Call\Call;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\RequestInterface;

/**
 * Class CallTest
 * @package Unit\Handler
 */
class CallTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Call
     */
    private $systemUnderTest;
    
    protected function setUp()
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
     * @expectedException \RuntimeException
     * @expectedExceptionMessage Invalid state. Call has an exception within.
     */
    public function shouldThrowExceptionWhenSetResponseAfterException()
    {
        $exceptionMock = $this->prophesize(RequestException::class)->reveal();
        $this->systemUnderTest->setException($exceptionMock);
    
        $response = new Response(201, [], '{"contactId":"one","email":"test@email.com"}');
        $this->systemUnderTest->setResponse($response);
    }
    
    /**
     * @test
     * @expectedException \RuntimeException
     * @expectedExceptionMessage Exception cannot be overwritten.
     */
    public function shouldThrowExceptionWhenOverwriteException()
    {
        $this->systemUnderTest->setException($this->prophesize(RequestException::class)->reveal());
        $this->systemUnderTest->setException($this->prophesize(RequestException::class)->reveal());
    }
    
    /**
     * @test
     * @expectedException \RuntimeException
     * @expectedExceptionMessage Invalid state. Call has a response within.
     */
    public function shouldThrowExceptionWhenSetExceptionAfterResponse()
    {
        $response = new Response(201, [], '{"contactId":"one","email":"test@email.com"}');
        $this->systemUnderTest->setResponse($response);
    
        $exceptionMock = $this->prophesize(RequestException::class)->reveal();
        $this->systemUnderTest->setException($exceptionMock);
    }
    
    /**
     * @test
     * @expectedException \RuntimeException
     * @expectedExceptionMessage Response cannot be overwritten.
     */
    public function shouldThrowExceptionWhenOverwriteResponse()
    {
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
