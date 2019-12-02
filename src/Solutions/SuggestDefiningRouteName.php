<?php

namespace Laracatch\Client\Solutions;

class SuggestDefiningRouteName extends BaseSolution
{
    /**
     * @return string
     */
    public function getTitle(): string
    {
        return 'Define the route name';
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return "Route names must be defined using the `name()` method. E.g. `Route::get('user/profile', 'UserProfileController@show')->name('profile')`.";
    }

    public function getLinks(): array
    {
        return [
            'Named Routes' => 'https://laravel.com/docs/6.x/routing#named-routes'
        ];
    }
}
