<?php

namespace Laracatch\Client\Solutions;

class SuggestRunningMigrations extends BaseSolution
{
    /**
     * @return string
     */
    public function getTitle(): string
    {
        return 'Run migrations';
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return 'If you are missing a table or a field that should be there, you can run migrations to generate the schema with `php artisan migrate`.';
    }

    public function getLinks(): array
    {
        return [
            'Running Migrations' => 'https://laravel.com/docs/6.x/migrations#running-migrations'
        ];
    }
}