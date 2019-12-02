<?php

namespace Laracatch\Client\Solutions;

class SuggestDefiningView extends BaseSolution
{
    /**
     * @return string
     */
    public function getTitle(): string
    {
        return 'Define the view';
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return "Ensure the view you're trying to use is defined. Views can be defined by creating a `.blade.php` file in a location Laravel knows to look for them. By default this is `resources/views`." ;
    }

    public function getLinks(): array
    {
        return [
            'Creating Views' => 'https://laravel.com/docs/6.x/views#creating-views'
        ];
    }
}