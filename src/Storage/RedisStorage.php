<?php

namespace Laracatch\Client\Storage;

use Carbon\Carbon;
use Laracatch\Client\Contracts\StorageContract;
use Predis\Client as RedisClient;

/**
 * Stores collected data into Redis
 */
class RedisStorage extends Storage implements StorageContract
{
    use ImplementsComparison,
        ImplementsFiltering;

    /** @var RedisClient */
    protected $redis;

    /** @var string */
    protected $hash;

    /**
     * @param RedisClient $redis Redis Client
     * @param string $hash
     */
    public function __construct(RedisClient $redis, string $hash = 'laracatch')
    {
        parent::__construct();

        $this->redis = $redis;
        $this->hash = $hash;
    }

    /**
     * {@inheritdoc}
     */
    public function save(array $data): ?string
    {
        $id = $this->generateIdentifier();

        $this->redis->hset("$this->hash:data", $id, json_encode($data));

        $this->garbageCollect();

        return $id;
    }

    /**
     * {@inheritdoc}
     */
    public function find($id): ?array
    {
        $data = $this->redis->hget("$this->hash:data", $id);

        if ( ! $data) {
            return null;
        }

        $data = json_decode($data, true);
        $data['id'] = $id;

        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function get(array $filters = [], $max = 20, $offset = 0): array
    {
        $results = [];
        $cursor = '0';

        do {
            [$cursor, $items] = $this->redis->hscan("$this->hash:data", $cursor);

            foreach ($items as $id => $data) {
                if ($data = json_decode($data, true)) {
                    if ($this->filter($data, $filters)) {
                        $data['id'] = $id;
                        $results[] = $data;
                    }
                }
            }
        } while ($cursor);

        usort($results, static function ($a, $b) {
            return $a['seen_at'] < $b['seen_at'];
        });

        return array_slice($results, $offset, $max);
    }


    /**
     * {@inheritdoc}
     */
    public function clear(): void
    {
        $this->redis->del("$this->hash:data");
    }

    /**
     * Garbage collect old data
     *
     * @return void
     */
    public function garbageCollect(): void
    {
        $cursor = '0';

        do {
            [$cursor, $items] = $this->redis->hscan("$this->hash:data", $cursor);

            foreach ($items as $id => $data) {
                if ($data = json_decode($data, true)) {
                    if (Carbon::createFromTimestamp($data['seen_at_microseconds'])->addHours($this->retention)->isBefore(Carbon::now())) {
                        $this->redis->hdel("$this->hash:data", $id);
                    }
                }
            }
        } while ($cursor);
    }
}
