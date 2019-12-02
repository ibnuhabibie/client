<?php

namespace Laracatch\Client\Solutions;

class SuggestDefiningRoute extends BaseSolution
{
    /**
     * @return string
     */
    public function getTitle(): string
    {
        return 'Define a new route';
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return "Define a new route using the method you need. E.g. `Route::post('post-route', 'PostController@store');`";
    }

    public function getLinks(): array
    {
        return [
            'Basic Routing' => 'https://laravel.com/docs/6.x/routing#basic-routing'
        ];
    }
}