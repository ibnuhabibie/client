<?php

namespace Laracatch\Client\DataProviders\Http;

use Illuminate\Contracts\Foundation\Application;
use Laracatch\Client\Contracts\DataProviderContract;
use Laracatch\Client\Models\ErrorModel;
use Throwable;

class HttpRequestDurationDataProvider implements DataProviderContract
{
    /**
     * @var int[]
     */
    protected static $times = [
        'hour' => 3600000,
        'minute' => 60000,
        'second' => 1000,
    ];

    /**
     * @param ErrorModel $errorModel
     * @param Application $app
     * @param Throwable|null $throwable
     *
     * @return mixed
     */
    public function handle(ErrorModel $errorModel, Application $app, Throwable $throwable = null)
    {
        $duration = $errorModel->seen_at_microseconds - LARAVEL_START;

        $errorModel->setPerformanceTime([
            'start' => LARAVEL_START,
            'end' => $errorModel->seen_at_microseconds,
            'duration' => $duration,
            'human_duration' => self::secondsToTimeString($duration),
        ]);
    }

    /**
     * @param float $time
     *
     * @return string
     */
    public static function secondsToTimeString(float $time): string
    {
        $ms = round($time * 1000);

        foreach (self::$times as $unit => $value) {
            if ($ms >= $value) {
                $time = floor($ms / $value * 100.0) / 100.0;

                return $time . ' ' . ($time == 1 ? $unit : $unit . 's');
            }
        }

        return $ms . ' ms';
    }
}