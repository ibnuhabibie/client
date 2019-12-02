<?php

namespace Laracatch\Client\Solutions;

class SuggestSupplyingParameters extends BaseSolution
{
    /**
     * @return string
     */
    public function getTitle(): string
    {
        return 'Supply parameters to URL generation';
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return "When generating URLs for routes that have parameters, you can pass the parameters in as an array. E.g. `route('profile', ['id' => 1])`";
    }

    public function getLinks(): array
    {
        return [
            'Named Routes' => 'https://laravel.com/docs/6.x/routing#named-routes'
        ];
    }
}
