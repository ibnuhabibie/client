<?php

namespace Laracatch\Client\Storage;

use Illuminate\Support\Str;

trait ImplementsComparison
{
    /**
     * Compare variables.
     *
     * @param mixed $var1
     * @param string $op
     * @param mixed $var2
     *
     * @return bool
     */
    protected function compare($var1, $op, $var2): bool
    {
        switch ($op) {
            case 'like':
                return Str::contains($var1, $var2);
            case 'eq':
                return $var1 == $var2;
            case 'neq':
                return $var1 != $var2;
            case 'gte':
                return $var1 >= $var2;
            case 'lte':
                return $var1 <= $var2;
            case 'gt':
                return $var1 > $var2;
            case 'lt':
                return $var1 < $var2;
            default:
                return true;
        }
    }
}
