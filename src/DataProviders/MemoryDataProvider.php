<?php

namespace Laracatch\Client\DataProviders;

use Illuminate\Contracts\Foundation\Application;
use Laracatch\Client\Contracts\DataProviderContract;
use Laracatch\Client\Models\ErrorModel;
use Throwable;

class MemoryDataProvider implements DataProviderContract
{
    /** @var array */
    private static $sizes = [
        'GB' => 1024 * 1024 * 1024,
        'MB' => 1024 * 1024,
        'KB' => 1024,
    ];

    /**
     * Handle providing memory data.
     *
     * @param ErrorModel $errorModel
     * @param Application $app
     * @param Throwable|null $throwable
     *
     * @return mixed
     */
    public function handle(ErrorModel $errorModel, Application $app, Throwable $throwable = null)
    {
        $peakUsage = memory_get_peak_usage(false);

        $errorModel->setPerformanceMemory([
            'peak_usage' => $peakUsage,
            'human_peak_usage' => self::bytesToString($peakUsage)
        ]);
    }

    /**
     * Convert bytes to a string.
     *
     * @param float $bytes
     *
     * @return string
     */
    public static function bytesToString(float $bytes): string
    {
        foreach (self::$sizes as $unit => $value) {
            if ($bytes >= $value) {
                return sprintf('%.2f %s', $bytes >= 1024 ? $bytes / $value : $bytes, $unit);
            }
        }

        return $bytes . ' byte' . ((int)$bytes !== 1 ? 's' : '');
    }
}