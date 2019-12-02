<?php

namespace Laracatch\Client\DefinedErrors;

use Illuminate\Support\Str;
use InvalidArgumentException;
use Laracatch\Client\Contracts\DefinedErrorContract;
use Laracatch\Client\Solutions\SuggestDefiningView;
use Throwable;

class ViewMissing implements DefinedErrorContract
{
    /**
     * @param Throwable $error
     *
     * @return bool
     */
    public function match(Throwable $error): bool
    {
        if ( ! $error instanceof InvalidArgumentException) {
            return false;
        }

        return Str::startsWith($error->getMessage(), 'View [');
    }

    /**
     * @param Throwable $error
     *
     * @return array
     */
    public function getSolutions(Throwable $error): array
    {
        return [
            new SuggestDefiningView()
        ];
    }
}