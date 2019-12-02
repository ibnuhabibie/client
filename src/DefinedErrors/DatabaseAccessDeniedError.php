<?php

namespace Laracatch\Client\DefinedErrors;

use Illuminate\Database\QueryException;
use Laracatch\Client\Contracts\DefinedErrorContract;
use Laracatch\Client\Solutions\SuggestCheckDbCredentialsSolution;
use Throwable;

class DatabaseAccessDeniedError implements DefinedErrorContract
{

    /**
     * @param Throwable $error
     * @return bool
     */
    public function match(Throwable $error): bool
    {
        if ( ! $error instanceof QueryException)
        {
            return false;
        }

        // https://dev.mysql.com/doc/refman/8.0/en/server-error-reference.html
        // Error number: 1045; Symbol: ER_ACCESS_DENIED_ERROR; SQLSTATE: 28000
        return $error->getCode() == 1045;
    }

    /**
     * @param Throwable $error
     * @return array
     */
    public function getSolutions(Throwable $error): array
    {
        return [
            new SuggestCheckDbCredentialsSolution()
        ];
    }
}