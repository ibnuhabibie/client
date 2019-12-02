<?php

namespace Laracatch\Client\Solutions;

use Laracatch\Client\Contracts\SolutionContract;

class SuggestCheckDbCredentialsSolution extends BaseSolution
{

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return 'Database connection credentials seem to be invalid';
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return 'Check your `.env` file and make sure the DB_DATABASE, DB_USERNAME and DB_PASSWORD variables are accurate.';
    }
}
