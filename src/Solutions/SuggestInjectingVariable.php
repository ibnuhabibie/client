<?php

namespace Laracatch\Client\Solutions;

class SuggestInjectingVariable extends BaseSolution
{
    /**
     * @return string
     */
    public function getTitle(): string
    {
        return 'Inject the variable into the view';
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return "You must inject variables into views before you can use them. The simplest way is to pass an array as the second parameter of the view call, such as `return view('greetings', ['name' => 'Victoria'])`.";
    }

    /**
     * @return array
     */
    public function getLinks(): array
    {
        return [
            'Passing Data To Views' => 'https://laravel.com/docs/6.x/views#passing-data-to-views'
        ];
    }
}