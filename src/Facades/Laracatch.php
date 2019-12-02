<?php

namespace Laracatch\Client\Facades;

use Illuminate\Support\Facades\Facade;
use Psr\Log\LogLevel;

/**
 * Class Laracatch
 *
 * @package Laracatch\Client\Facades
 *
 * @method static void breadcrumb(string $name, string $logLevel = LogLevel::INFO, array $meta = [])
 */
class Laracatch extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'laracatch.facade';
    }
}
