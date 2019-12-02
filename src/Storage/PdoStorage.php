<?php

namespace Laracatch\Client\Storage;

use Carbon\Carbon;
use Illuminate\Support\Arr;
use Laracatch\Client\Contracts\StorageContract;
use PDO;

class PdoStorage extends Storage implements StorageContract
{
    /** @var PDO */
    protected $pdo;

    /** @var string */
    protected $tableName;

    /** @var array */
    protected $sqlQueries = [
        'save' => "INSERT INTO %tablename% (id, data, seen_at, location, console, ip, method) VALUES (?, ?, ?, ?, ?, ?, ?)",
        'find' => "SELECT data FROM %tablename% WHERE id = ?",
        'get' => "SELECT id, data FROM %tablename% %where% ORDER BY seen_at DESC LIMIT %limit% OFFSET %offset%",
        'clear' => "DELETE FROM %tablename%",
        'garbage' => "DELETE FROM %tablename% WHERE seen_at < ?"
    ];

    /**
     * @param PDO $pdo
     * @param string $tableName
     * @param array $sqlQueries
     */
    public function __construct(PDO $pdo, string $tableName = 'laracatch', array $sqlQueries = [])
    {
        parent::__construct();

        $this->pdo = $pdo;
        $this->tableName = $tableName;
        $this->setSqlQueries($sqlQueries);
    }

    /**
     * Sets the sql queries to be used
     *
     * @param array $queries
     */
    public function setSqlQueries(array $queries)
    {
        $this->sqlQueries = array_merge($this->sqlQueries, $queries);
    }

    /**
     * {@inheritdoc}
     */
    public function save(array $data): ?string
    {
        $id = $this->generateIdentifier();

        $sql = $this->getSqlQuery('save');
        $stmt = $this->pdo->prepare($sql);

        $stmt->execute([
            $id,
            json_encode($data),
            $data['seen_at'],
            $data['location'],
            (int)$data['console'],
            $data['ip'],
            $data['method']
        ]);

        $this->garbageCollect();

        return $id;
    }

    /**
     * Get a SQL Query for a task, with the variables replaced
     *
     * @param string $name
     * @param array $vars
     *
     * @return string
     */
    protected function getSqlQuery($name, array $vars = []): string
    {
        $sql = $this->sqlQueries[$name];
        $vars = array_merge(['tablename' => $this->tableName], $vars);

        foreach ($vars as $k => $v) {
            $sql = str_replace("%$k%", $v, $sql);
        }

        return $sql;
    }

    /**
     * Garbage collect old data
     */
    public function garbageCollect(): void
    {
        $sql = $this->getSqlQuery('garbage');
        $stmt = $this->pdo->prepare($sql);

        $stmt->execute([Carbon::now()->subHours($this->retention)->timestamp]);
    }

    /**
     * {@inheritdoc}
     */
    public function find($id): ?array
    {
        $sql = $this->getSqlQuery('find');

        $stmt = $this->pdo->prepare($sql);

        $stmt->execute([$id]);

        if (($data = $stmt->fetchColumn(0)) !== false) {
            return json_decode($data, true);
        }

        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function get(array $filters = [], $max = 20, $offset = 0): array
    {
        $where = [];
        $params = [];

        foreach ($filters as $key => $value) {
            $field = Arr::first(explode('.', $key));

            $operator = $this->operator(Arr::last(explode('.', $key)) === $field ? '=' : Arr::last(explode('.', $key)));

            if ($operator === 'LIKE') {
                $where[] = "$field $operator %?%";
            } else {
                $where[] = "$field $operator ?";
            }

            $params[] = $value;
        }

        if (count($where)) {
            $where = ' WHERE ' . implode(' AND ', $where);
        } else {
            $where = '';
        }

        $sql = $this->getSqlQuery('get', [
            'where' => $where,
            'offset' => $offset,
            'limit' => $max
        ]);

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);

        $results = [];

        foreach ($stmt->fetchAll() as $row) {
            $data = json_decode($row['data'], true);
            $data['id'] = $row['id'];

            $results[] = $data;
        }
        return $results;
    }

    /**
     * Convert our operators to SQL operators.
     *
     * @param string $op
     *
     * @return string
     */
    protected function operator(string $op): ?string
    {
        switch ($op) {
            case 'like':
                return 'LIKE';
            case 'neq':
                return '<>';
            case 'gte':
                return '>=';
            case 'lte':
                return '<=';
            case 'gt':
                return '>';
            case 'lt':
                return '<';
            case 'eq':
            default:
                return '=';
        }
    }

    /**
     * {@inheritdoc}
     */
    public function clear(): void
    {
        $this->pdo->exec($this->getSqlQuery('clear'));
    }
}
