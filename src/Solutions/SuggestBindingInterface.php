<?php

namespace Laracatch\Client\Solutions;

class SuggestBindingInterface extends BaseSolution
{
    /**
     * @return string
     */
    public function getTitle(): string
    {
        return 'Bind interface to concrete implementation';
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return 'Use the Laravel container to bind a concrete implementation to your interface. E.g. `$this->app->bind(\'App\\Contracts\\EventPusher\', \'App\\Services\\RedisEventPusher\');`';
    }

    public function getLinks(): array
    {
        return [
            'Binding Interfaces To Implementations' => 'https://laravel.com/docs/6.x/container#binding-interfaces-to-implementations'
        ];
    }
}
