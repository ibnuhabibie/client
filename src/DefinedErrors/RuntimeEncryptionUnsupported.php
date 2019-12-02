<?php

namespace Laracatch\Client\DefinedErrors;

use Illuminate\Support\Str;
use Laracatch\Client\Contracts\DefinedErrorContract;
use Laracatch\Client\Solutions\SuggestCheckingAppKey;
use RuntimeException;
use Throwable;

class RuntimeEncryptionUnsupported implements DefinedErrorContract
{
    /**
     * @param Throwable $error
     *
     * @return bool
     */
    public function match(Throwable $error): bool
    {
        if (! $error instanceof RuntimeException) {
            return false;
        }

        return Str::contains(
            $error->getMessage(),
            'The only supported ciphers are AES-128-CBC and AES-256-CBC with the correct key lengths'
        );
    }

    /**
     * @param Throwable $error
     *
     * @return array
     */
    public function getSolutions(Throwable $error): array
    {
        return [
            new SuggestCheckingAppKey()
        ];
    }
}
