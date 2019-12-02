<?php

namespace Laracatch\Client\Contracts;

interface StorageContract
{
    /**
     * @param array $data
     *
     * @return string|null
     */
    public function save(array $data): ?string;

    /**
     * Return data with the specified id
     *
     * @param string $id
     *
     * @return array|null
     */
    public function find($id): ?array;

    /**
     * Return a slice of collected data
     *
     * @param array $filters
     * @param integer $max
     * @param integer $offset
     *
     * @return array
     */
    public function get(array $filters = [], $max = 20, $offset = 0): array;

    /**
     * Clear all the data
     *
     * @return void
     */
    public function clear(): void;

    /**
     * Garbage collect old data
     *
     * @return void
     */
    public function garbageCollect(): void;
}