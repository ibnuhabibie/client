<?php

namespace Laracatch\Client\DefinedErrors;

use Illuminate\Support\Str;
use Laracatch\Client\Contracts\DefinedErrorContract;
use Laracatch\Client\Solutions\SuggestChangingFormMethod;
use Laracatch\Client\Solutions\SuggestDefiningRoute;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Throwable;

class RouteMethodNotAllowed implements DefinedErrorContract
{
    /**
     * @param Throwable $error
     *
     * @return bool
     */
    public function match(Throwable $error): bool
    {
        if (! $error instanceof MethodNotAllowedHttpException) {
            return false;
        }

        return Str::contains($error->getMessage(), 'method is not supported for this route');
    }

    /**
     * @param Throwable $error
     *
     * @return array
     */
    public function getSolutions(Throwable $error): array
    {
        return [
            new SuggestChangingFormMethod(),
            new SuggestDefiningRoute()
        ];
    }
}
