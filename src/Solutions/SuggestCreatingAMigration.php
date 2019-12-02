<?php

namespace Laracatch\Client\Solutions;

class SuggestCreatingAMigration extends BaseSolution
{
    /**
     * @return string
     */
    public function getTitle(): string
    {
        return 'Create a new migration';
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return 'You can create a new migration by running `php artisan migrate:make <name-of-migration>`.';
    }

    public function getLinks(): array
    {
        return [
            'Creating Migrations' => 'https://laravel.com/docs/6.x/migrations#generating-migrations'
        ];
    }
}
