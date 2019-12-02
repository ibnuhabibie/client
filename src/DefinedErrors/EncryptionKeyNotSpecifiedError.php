<?php

namespace Laracatch\Client\DefinedErrors;

use Laracatch\Client\Contracts\DefinedErrorContract;
use Laracatch\Client\Solutions\SuggestKeyGenerateSolution;
use Throwable;
use RuntimeException;

class EncryptionKeyNotSpecifiedError implements DefinedErrorContract
{

    /**
     * @param Throwable $error
     * @return bool
     */
    public function match(Throwable $error): bool
    {
        if (! $error instanceof RuntimeException) {
            return false;
        }

        return $error->getMessage() === 'No application encryption key has been specified.';
    }

    /**
     * @param Throwable $error
     * @return array
     */
    public function getSolutions(Throwable $error): array
    {
        return [
            new SuggestKeyGenerateSolution()
        ];
    }
}
