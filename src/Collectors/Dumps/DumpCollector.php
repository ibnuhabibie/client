<?php

namespace Laracatch\Client\Collectors\Dumps;

use Laracatch\Client\Contracts\DumpCollectorContract;
use Symfony\Component\VarDumper\Cloner\Data;
use Symfony\Component\VarDumper\Cloner\VarCloner;
use Symfony\Component\VarDumper\Dumper\CliDumper;
use Symfony\Component\VarDumper\Dumper\HtmlDumper as BaseHtmlDumper;
use Symfony\Component\VarDumper\VarDumper;
use Illuminate\Contracts\Foundation\Application;

class DumpCollector implements DumpCollectorContract
{
    /** @var Application */
    protected $app;

    /** @var array */
    protected $dumps = [];

    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    /**
     * Collect dump data.
     *
     * @param Data $data
     *
     * @return void
     */
    public function collect(Data $data): void
    {
        if ( ! $this->app['config']->get('laracatch.collectors.dumps')) {
            return;
        }

        $backtrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 7);

        $backtraceIndex = $this->isDdd($backtrace) ? 6 : 5;

        $file = $backtrace[$backtraceIndex]['file'] ?? null;
        $line = $backtrace[$backtraceIndex]['line'] ?? null;

        $this->dumps[] = new Dump((new HtmlDumper)->dump($data), $data, $file, $line);
    }

    /**
     * Get dumps.
     *
     * @return array
     */
    public function getItems(): array
    {
        return array_map(static function (Dump $dump) {
           return $dump->toArray();
        }, $this->dumps);
    }

    /**
     * Reset dumps.
     *
     * @return void
     */
    public function reset(): void
    {
        $this->dumps = [];
    }

    /**
     * Register the collector as a handler for dumping.
     *
     * @return void
     */
    public function listen(): void
    {
        $dumpHandlers = new DumpCollectionHandlerGroup;

        $originalHandler = VarDumper::setHandler(static function ($var) use ($dumpHandlers) {
            $dumpHandlers->dump($var);
        });

        $dumpHandlers->add($originalHandler ?? $this->getDefaultHandler());

        $dumpHandlers->add(function ($var) {
            // self
            $this->app->make(DumpCollectorContract::class)->collect((new VarCloner)->cloneVar($var));
        });
    }

    /**
     * Get a default handler for dumping.
     *
     * @return callable
     */
    protected function getDefaultHandler(): callable
    {
        return static function ($value) {
            $data = (new VarCloner)->cloneVar($value);

            $dumper = in_array(PHP_SAPI, ['cli', 'phpdbg']) ? new CliDumper : new BaseHtmlDumper;

            $dumper->dump($data);
        };
    }

    /**
     * Check if the dump comes from the ddd() function.
     *
     * This is necessary because using ddd() to create dumps adds an extra call to the stack, which
     * changes the index we need to use to determine what file/line the dump came from.
     *
     * @param array $backtrace
     *
     * @return bool
     */
    protected function isDdd(array $backtrace): bool
    {
        return isset($backtrace[6]) && $backtrace[6]['function'] === 'ddd';
    }
}