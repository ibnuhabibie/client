<?php

namespace Laracatch\Client\Collectors;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Database\Events\QueryExecuted;
use Illuminate\Support\Str;
use Laracatch\Client\Contracts\QueryCollectorContract;

class QueryCollector implements QueryCollectorContract
{
    /** @var array */
    protected $queries = [];

    /** @var Application */
    protected $app;

    /** @var bool */
    protected $bindings = false;

    public function __construct(Application $app)
    {
        $this->app = $app;

        if ($this->app['config']->get('laracatch.data_providers.report_query_bindings')) {
            $this->bindings = true;
        }
    }

    /**
     * @return mixed
     */
    public function listen()
    {
        return $this->app['events']->listen(QueryExecuted::class, function (QueryExecuted $queryExecuted) {
            $this->record($queryExecuted);
        });
    }

    /**
     * @param QueryExecuted $queryExecuted
     */
    protected function record(QueryExecuted $queryExecuted): void
    {
        $this->queries[] = $this->format($queryExecuted);
    }

    /**
     * @param QueryExecuted $queryExecuted
     *
     * @return array
     */
    protected function format(QueryExecuted $queryExecuted): array
    {
        return [
            'sql' => $this->bindings ? Str::replaceArray(
                '?',
                $queryExecuted->bindings,
                $queryExecuted->sql
            ) : $queryExecuted->sql,
            'time' => $queryExecuted->time,
            'measure_unit' => 'ms',
            'connection_name' => $queryExecuted->connectionName,
            'microtime' => microtime(true),
        ];
    }

    /**
     * @return array
     */
    public function getItems(): array
    {
        return $this->queries;
    }

    /**
     * @return void
     */
    public function reset(): void
    {
        $this->queries = [];
    }
}
