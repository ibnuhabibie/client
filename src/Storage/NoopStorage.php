<?php

namespace Laracatch\Client\Storage;

use Laracatch\Client\Contracts\StorageContract;

class NoopStorage extends Storage implements StorageContract
{
    /**
     * {@inheritdoc}
     */
    public function save(array $data): ?string
    {
        return null;
    }

    /**
     * Return data with the specified id
     *
     * @param string $id
     *
     * @return array|null
     */
    public function find($id): ?array
    {
        return null;
    }

    /**
     * Return a slice of collected data
     *
     * @param array $filters
     * @param integer $max
     * @param integer $offset
     *
     * @return array
     */
    public function get(array $filters = [], $max = 20, $offset = 0): array
    {
        return [];
    }

    /**
     * Clear all the data
     */
    public function clear(): void
    {
        // ...
    }

    /**
     * Garbage collect old data
     */
    public function garbageCollect(): void
    {
        // ...
    }
}
