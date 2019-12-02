<?php

namespace Laracatch\Client\View\Engines;

use Exception;
use Illuminate\View\Engines\PhpEngine;
use Laracatch\Client\Exceptions\ViewException;
use Laracatch\Client\Traits\GathersViewExceptionData;

class LaracatchPhpEngine extends PhpEngine
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
     * @param Exception $originalException
     * @param int $obLevel
     *
     * @return void
     *
     * @throws \Exception
     */
    protected function handleViewException(Exception $originalException, $obLevel)
    {
        $exception = new ViewException(
            $originalException->getMessage(),
            0,
            1,
            $this->getOriginalViewPath($originalException->getFile()),
            $this->getBladeLineNumber($originalException->getFile(), $originalException->getLine()),
            $originalException
        );

        $exception->setPath($this->getOriginalViewPath($originalException->getFile()));
        $exception->setData($this->getOriginalViewData($originalException->getFile()));

        parent::handleViewException($exception, $obLevel);
    }
}