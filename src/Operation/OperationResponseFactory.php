<?php
namespace Getresponse\Sdk\Client\Operation;

use Getresponse\Sdk\Client\Handler\Call\Call;

/**
 * Class OperationResponseFactory
 * @package Getresponse\Sdk\Client\Operation
 */
class OperationResponseFactory
{
    /**
     * @param Call $call
     * @return OperationResponse
     */
    public static function createByCall(Call $call)
    {
        if ($call->isSucceeded()) {
            return new SuccessfulOperationResponse(
                $call->getResponse(),
                $call->getSuccessCode(),
                $call->getRequest()
            );
        }
        if (!$call->isFinished()) {
            return FailedOperationResponse::createAsIncomplete(
                $call->getRequest()
            );
        }
        if ($call->hasException()) {
            return FailedOperationResponse::createWithException(
                $call->getException(),
                $call->getRequest()
            );
        }
        return FailedOperationResponse::createWithResponse(
            $call->getResponse(),
            $call->getRequest()
        );
    }
}