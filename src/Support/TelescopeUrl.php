<?php

namespace Laracatch\Client\Support;

use Laravel\Telescope\Http\Controllers\HomeController;
use Laravel\Telescope\IncomingExceptionEntry;
use Laravel\Telescope\Telescope;

class TelescopeUrl
{
    public static function get(): ?string
    {
        try {
            if ( ! class_exists(Telescope::class)) {
                return null;
            }

            if ( ! count(Telescope::$entriesQueue)) {
                return null;
            }

            $telescopeEntry = collect(Telescope::$entriesQueue)->first(function ($entry) {
                return $entry instanceof IncomingExceptionEntry;
            });

            if (is_null($telescopeEntry)) {
                return null;
            }

            $telescopeEntryId = (string)$telescopeEntry->uuid;

            return url(action([HomeController::class, 'index']) . "/exceptions/{$telescopeEntryId}");
        } catch (\Exception $exception) {
            return null;
        }
    }
}