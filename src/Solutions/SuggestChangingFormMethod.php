<?php

namespace Laracatch\Client\Solutions;

class SuggestChangingFormMethod extends BaseSolution
{
    /**
     * @return string
     */
    public function getTitle(): string
    {
        return 'Change form method';
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return 'If you are trying to submit a form to an endpoint, check that the form is defined to use the correct method (commonly `POST` or `PUT`) for your route.';
    }

    public function getLinks(): array
    {
        return [
            'Forms' => 'https://laravel.com/docs/6.x/blade#forms'
        ];
    }
}
