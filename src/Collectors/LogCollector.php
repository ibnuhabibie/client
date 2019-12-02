<?php

namespace Laracatch\Client\Collectors;

use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Log\Events\MessageLogged;
use Laracatch\Client\Contracts\LogCollectorContract;
use Laracatch\Client\Recorders\Log\LogMessage;

class LogCollector implements LogCollectorContract
{
    /**
     * @var array
     */
    protected $logs = [];

    /**
     * @var Application
     */
    protected $app;

    /**
     * @param Application $app
     */
    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    /**
     * @return mixed
     */
    public function listen()
    {
        return $this->app['events']->listen(MessageLogged::class, function (MessageLogged $log) {
            $this->record($log);
        });
    }

    /**
     * @param MessageLogged $log
     */
    protected function record(MessageLogged $log)
    {
        if ($this->shouldIgnore($log)) {
            return;
        }

        $this->logs[] = [
            'message' => $log->message,
            'level' => $log->level,
            'context' => $log->context,
            'microtime' => microtime(true),
        ];
    }

    /**
     * @param $event
     *
     * @return bool
     */
    protected function shouldIgnore($event)
    {
        if (! isset($event->context['exception'])) {
            return false;
        }

        if (! $event->context['exception'] instanceof Exception) {
            return false;
        }

        return true;
    }

    /**
     * @return array
     */
    public function getItems()
    {
        return $this->logs;
    }

    /**
     * @return void
     */
    public function reset()
    {
        $this->logs = [];
    }
}
