<?php

namespace Laracatch\Client\Client\Exceptions;

use Laracatch\Client\Client\Response;

class InvalidData extends BadResponseCode
{
    public static function getMessageForResponse(Response $response)
    {
        return 'Invalid data found';
    }
}
