<?php

namespace Laracatch\Client\Client\Exceptions;

use Exception;
use Laracatch\Client\Client\Response;

class BadResponse extends Exception
{
    /** @var Response */
    public $response;

    public static function createForResponse(Response $response)
    {
        $exception = new static("Could not perform request because: {$response->getError()}");

        $exception->response = $response;

        return $exception;
    }
}
