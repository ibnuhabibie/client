<?php

namespace Laracatch\Client\Client\Exceptions;

use Exception;
use Laracatch\Client\Client\Response;

class BadResponseCode extends Exception
{
    /** @var Response */
    public $response;

    /** @var array */
    public $errors;

    public static function createForResponse(Response $response)
    {
        $exception = new static(static::getMessageForResponse($response));

        $exception->response = $response;

        $bodyErrors = isset($response->getBody()['errors']) ? $response->getBody()['errors'] : [];

        $exception->errors = $bodyErrors;

        return $exception;
    }

    public static function getMessageForResponse(Response $response)
    {
        return "Response code {$response->getHttpResponseCode()} returned";
    }
}
