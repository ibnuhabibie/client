<?php

namespace Laracatch\Client\DefinedErrors;

use Illuminate\Support\Str;
use Laracatch\Client\Contracts\DefinedErrorContract;
use Laracatch\Client\Exceptions\ViewException;
use Laracatch\Client\Solutions\SuggestInjectingVariable;
use Throwable;

class ViewExceptionMissingVariable implements DefinedErrorContract
{
    /**
     * @param Throwable $error
     *
     * @return bool
     */
    public function match(Throwable $error): bool
    {
        if ( ! $error instanceof ViewException) {
            return false;
        }

        return Str::contains($error->getMessage(), 'Undefined variable:');
    }

    /**
     * @param Throwable $error
     *
     * @return array
     */
    public function getSolutions(Throwable $error): array
    {
        return [
            new SuggestInjectingVariable()
        ];
    }
}