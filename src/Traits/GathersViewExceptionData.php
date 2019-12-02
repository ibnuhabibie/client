<?php

namespace Laracatch\Client\Traits;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Foundation\Application;
use Illuminate\Support\Arr;
use Illuminate\View\Engines\CompilerEngine;
use Laracatch\Client\View\Engines\BladeSourceMapCompiler;

trait GathersViewExceptionData
{
    /**
     * A stack of the last original and compiled views.
     *
     * @var array
     */
    protected $lastCompiledViews = [];

    /**
     * Gather the data for a view exception.
     *
     * @param string $path
     * @param array $data
     *
     * @return void
     */
    protected function gatherViewExceptionData(string $path, array $data = []): void
    {
        $this->lastCompiledViews[] = [
            'original' => $path,
            'compiled' => $this->resolveCompiledPath($path),
            'data' => $this->applyDataFilters($data)
        ];
    }

    /**
     * Resolve the path for the compiled view.
     *
     * @param string $path
     *
     * @return string
     */
    protected function resolveCompiledPath(string $path): string
    {
        return $this instanceof CompilerEngine
            ? $this->getCompiler()->getCompiledPath($path)
            : $path;
    }

    /**
     * Apply filters to the data to remove unwanted keys.
     *
     * @param array $data
     *
     * @return array
     */
    protected function applyDataFilters(array $data): array
    {
        return array_filter($data, static function ($value, $key) {
            if ($key === 'app') {
                return ! $value instanceof Application;
            }

            return $key !== '__env';
        }, ARRAY_FILTER_USE_BOTH);
    }

    /**
     * Get the original name of the compiled view.
     *
     * @param string $compiledPath
     *
     * @return string
     */
    protected function getOriginalViewPath(string $compiledPath): string
    {
        $compiledView = $this->findByCompiledPath($compiledPath);

        return $compiledView['original'] ?? $compiledPath;
    }

    /**
     * Get the original data of the compiled view.
     *
     * @param string $compiledPath
     *
     * @return array
     */
    protected function getOriginalViewData(string $compiledPath): array
    {
        $compiledView = $this->findByCompiledPath($compiledPath);

        return $compiledView['data'] ?? [];
    }

    /**
     * Attempt to find the original view name by using the compiled version of it.
     *
     * @param string $compiledPath
     *
     * @return array|null
     */
    protected function findByCompiledPath(string $compiledPath): ?array
    {
        return Arr::first($this->lastCompiledViews, static function ($views) use ($compiledPath) {
            $comparePath = $views['compiled'];

            return realpath(dirname($comparePath)) . DIRECTORY_SEPARATOR . basename($comparePath) === $compiledPath;
        });
    }

    /**
     * Leverage the BladeSourceMapCompiler to detect the original line number from the exception.
     *
     * @param string $compiledPath
     * @param int $exceptionLineNumber
     *
     * @return int
     */
    protected function getBladeLineNumber(string $compiledPath, int $exceptionLineNumber): int
    {
        $viewPath = $this->getOriginalViewPath($compiledPath);

        if ( ! $viewPath) {
            return $exceptionLineNumber;
        }

        $sourceMapCompiler = new BladeSourceMapCompiler(app(Filesystem::class), 'ignore-cache-path');

        return $sourceMapCompiler->detectExceptionLineNumber($viewPath, $exceptionLineNumber);
    }
}