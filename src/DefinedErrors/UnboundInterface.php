<?php

namespace Laracatch\Client\DefinedErrors;

use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Support\Str;
use Laracatch\Client\Contracts\DefinedErrorContract;
use Laracatch\Client\Solutions\SuggestBindingInterface;
use Throwable;

class UnboundInterface implements DefinedErrorContract
{
    /**
     * @param Throwable $error
     *
     * @return bool
     */
    public function match(Throwable $error): bool
    {
        if (! $error instanceof BindingResolutionException) {
            return false;
        }

        return Str::contains($error->getMessage(), 'is not instantiable');
    }

    /**
     * @param Throwable $error
     *
     * @return array
     */
    public function getSolutions(Throwable $error): array
    {
        return [
            new SuggestBindingInterface()
        ];
    }
}
