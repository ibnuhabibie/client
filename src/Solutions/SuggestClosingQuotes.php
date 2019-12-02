<?php

namespace Laracatch\Client\Solutions;

class SuggestClosingQuotes extends BaseSolution
{
    /**
     * @return string
     */
    public function getTitle(): string
    {
        return 'Close quoted string';
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return 'Ensure that any strings have their closing quote.';
    }
}