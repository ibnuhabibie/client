<?php

namespace Laracatch\Client\View\Engines;

use Illuminate\Support\Arr;
use Illuminate\View\Engines\CompilerEngine;
use Laracatch\Client\Exceptions\ViewException;
use Laracatch\Client\Traits\GathersViewExceptionData;
use ReflectionException;
use ReflectionProperty;
use Throwable;

class LaracatchCompilerEngine extends CompilerEngine
{
    use GathersViewExceptionData;

    /**
     * Get the evaluated contents of the view.
     *
     * @param string $path
     * @param array $data
     *
     * @return string
     */
    public function get($path, array $data = [])
    {
        $this->gatherViewExceptionData($path, $data);

        return parent::get($path, $data);
    }

    /**
     * Handle a view exception.
     *
     * @param Throwable $originalException
     * @param int $obLevel
     *
     * @return void
     * @throws Throwable
     */
    protected function handleViewException(Throwable $originalException, $obLevel)
    {
        while (ob_get_level() > $obLevel) {
            ob_end_clean();
        }

        if ($originalException instanceof ViewException) {
            throw $originalException;
        }

        $exception = new ViewException(
            $this->getMessage($originalException),
            0,
            1,
            $this->getOriginalViewPath($originalException->getFile()),
            $this->getBladeLineNumber($originalException->getFile(), $originalException->getLine()),
            $originalException
        );

        $this->modifyViewsInTrace($exception);

        $exception->setPath($this->getOriginalViewPath($originalException->getFile()));
        $exception->setData($this->getOriginalViewData($originalException->getFile()));

        throw $exception;
    }

    /**
     * Update StackTrace to account for the Blade view.
     *
     * @param Throwable $exception
     *
     * @throws ReflectionException
     */
    protected function modifyViewsInTrace(Throwable $exception)
    {
        $trace = array_map(function ($trace) {
            if ($compiledView = $this->findByCompiledPath(Arr::get($trace, 'file', ''))) {
                $trace['file'] = $compiledView['original'];
                $trace['line'] = $this->getBladeLineNumber($trace['file'], $trace['line']);
            }

            return $trace;
        }, $exception->getPrevious()->getTrace());

        $traceProperty = new ReflectionProperty('Exception', 'trace');
        $traceProperty->setAccessible(true);
        $traceProperty->setValue($exception, $trace);
    }
}
