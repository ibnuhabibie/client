<?php

namespace Laracatch\Client\Support;

trait AttributeTypeSerializationTrait
{
    /**
     * Serialize the given value to a string.
     *
     * @param mixed $value
     *
     * @return mixed
     */
    protected function serializeValue($value)
    {
        if (is_array($value)) {
            return $this->serializeArray($value);
        }

        if (is_object($value)) {
            return get_class($value);
        }

        if (is_string($value)) {
            return strlen($value) > 500 ? substr($value, 0, 500) . '...' : $value;
        }

        if (is_int($value)) {
            return $value;
        }

        if (is_float($value)) {
            return $value;
        }

        if (is_bool($value)) {
            return $value ? 'true' : 'false';
        }

        if ($value instanceof \__PHP_Incomplete_Class) {
            return 'object(__PHP_Incomplete_Class)';
        }

        return gettype($value);
    }

    /**
     * Serialize an array.
     *
     * @param array $data
     *
     * @return array
     */
    protected function serializeArray(array $data): array
    {
        array_walk_recursive($data, function (&$value) {
            $value = $this->serializeValue($value);
        });

        return $data;
    }
}