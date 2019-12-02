<?php

namespace Laracatch\Client\Solutions;

class SuggestCheckingAppKey extends BaseSolution
{
    /**
     * @return string
     */
    public function getTitle(): string
    {
        return 'Check environment application key';
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return 'This error is often caused by an improperly set `APP_KEY` variable in your `.env` file. Try running `php artisan key:generate` in the CLI to generate a new one.';
    }
}