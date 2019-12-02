<?php

namespace Laracatch\Client\Client\Exceptions;

use Laracatch\Client\Client\Response;

class NotFound extends BadResponseCode
{
    public static function getMessageForResponse(Response $response)
    {
        return 'Not found';
    }
}
