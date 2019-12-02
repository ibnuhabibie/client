<?php

namespace Laracatch\Client\DefinedErrors;

use Illuminate\Database\QueryException;
use Illuminate\Support\Str;
use Laracatch\Client\Contracts\DefinedErrorContract;
use Laracatch\Client\Solutions\SuggestCreatingAMigration;
use Laracatch\Client\Solutions\SuggestRunningMigrations;
use Throwable;

class DatabaseMissingTable implements DefinedErrorContract
{
    /**
     * @param Throwable $error
     *
     * @return bool
     */
    public function match(Throwable $error): bool
    {
        if ( ! $error instanceof QueryException) {
            return false;
        }

        return Str::contains($error->getMessage(), 'SQLSTATE[42S02]: Base table or view not found');
    }

    /**
     * @param Throwable $error
     *
     * @return array
     */
    public function getSolutions(Throwable $error): array
    {
        return [
            new SuggestRunningMigrations(),
            new SuggestCreatingAMigration()
        ];
    }
}