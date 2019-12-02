<?php

namespace Laracatch\Client\Solutions;

use Laracatch\Client\Contracts\SolutionContract;

class SuggestKeyGenerateSolution extends BaseSolution
{

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return 'The `APP_KEY` configuration variable in the `.env` file is not specified';
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return 'Consider running the `php artisan key:generate` command';
    }

    /**
     * @return array
     */
    public function getLinks(): array
    {
        return [
            'Encryption Configuration' => 'https://laravel.com/docs/6.x/encryption#configuration'
        ];
    }
}
