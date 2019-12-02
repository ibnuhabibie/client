<?php

namespace Laracatch\Client\DefinedErrors;

use Illuminate\Routing\Exceptions\UrlGenerationException;
use Illuminate\Support\Str;
use Laracatch\Client\Contracts\DefinedErrorContract;
use Laracatch\Client\Solutions\SuggestSupplyingParameters;
use Throwable;

class RouteMissingParameters implements DefinedErrorContract
{
    /**
     * @param Throwable $error
     *
     * @return bool
     */
    public function match(Throwable $error): bool
    {
        if (! $error instanceof UrlGenerationException) {
            return false;
        }

        return Str::contains($error->getMessage(), 'Missing required parameters for');
    }

    /**
     * @param Throwable $error
     *
     * @return array
     */
    public function getSolutions(Throwable $error): array
    {
        return [
            new SuggestSupplyingParameters()
        ];
    }
}
