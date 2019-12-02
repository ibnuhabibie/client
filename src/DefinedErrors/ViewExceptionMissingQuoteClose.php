<?php

namespace Laracatch\Client\DefinedErrors;

use Illuminate\Support\Str;
use Laracatch\Client\Contracts\DefinedErrorContract;
use Laracatch\Client\Exceptions\ViewException;
use Laracatch\Client\Solutions\SuggestClosingQuotes;
use Throwable;

class ViewExceptionMissingQuoteClose implements DefinedErrorContract
{
    /**
     * @param Throwable $error
     *
     * @return bool
     */
    public function match(Throwable $error): bool
    {
        if (! $error instanceof ViewException) {
            return false;
        }

        return Str::contains($error->getMessage(), "unexpected '__data' (T_STRING), expecting ',' or ')'");
    }

    /**
     * @param Throwable $error
     *
     * @return array
     */
    public function getSolutions(Throwable $error): array
    {
        return [
            new SuggestClosingQuotes()
        ];
    }
}
