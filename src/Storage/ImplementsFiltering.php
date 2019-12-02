<?php

namespace Laracatch\Client\Storage;

use Illuminate\Support\Arr;

trait ImplementsFiltering
{
    /**
     * @param $data
     * @param $filters
     *
     * @return bool
     */
    protected function filter($data, $filters): bool
    {
        foreach ($filters as $key => $value) {
            $field = Arr::first(explode('.', $key));
            $operator = Arr::last(explode('.', $key)) === $field ? '=' : Arr::last(explode('.', $key));

            if ( ! isset($data[$field]) || ! $this->compare($data[$field], $operator, $value)) {
                return false;
            }
        }
        return true;
    }
}