<?php

namespace Laracatch\Client\DataProviders\Console;

use Illuminate\Contracts\Foundation\Application;
use Laracatch\Client\Contracts\DataProviderContract;
use Laracatch\Client\Models\ErrorModel;
use Throwable;

class ConsoleLocationDataProvider implements DataProviderContract
{
    /**
     * @param ErrorModel $errorModel
     * @param Application $app
     * @param Throwable|null $throwable
     *
     * @return mixed
     */
    public function handle(ErrorModel $errorModel, Application $app, Throwable $throwable = null)
    {
        $consoleCommand = isset($_SERVER['argv']) ? implode(' ', $_SERVER['argv']) : null;

        $errorModel->setLocationAttributes($consoleCommand);
    }
}