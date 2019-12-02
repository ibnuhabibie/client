<?php

namespace Laracatch\Client;

use Illuminate\Contracts\Foundation\Application;
use Laracatch\Client\Contracts\DefinedErrorContract;
use Laracatch\Client\Contracts\DefinedErrorHandlerContract;
use Laracatch\Client\Contracts\SolutionContract;
use Laracatch\Client\DefinedErrors\DatabaseAccessDeniedError;
use Laracatch\Client\DefinedErrors\DatabaseMissingField;
use Laracatch\Client\DefinedErrors\DatabaseMissingTable;
use Laracatch\Client\DefinedErrors\EncryptionKeyNotSpecifiedError;
use Laracatch\Client\DefinedErrors\RouteMissingParameters;
use Laracatch\Client\DefinedErrors\RouteNameNotDefined;
use Laracatch\Client\DefinedErrors\RouteMethodNotAllowed;
use Laracatch\Client\DefinedErrors\ViewExceptionMissingQuoteClose;
use Laracatch\Client\DefinedErrors\ViewExceptionMissingVariable;
use Laracatch\Client\DefinedErrors\RuntimeEncryptionUnsupported;
use Laracatch\Client\DefinedErrors\UnboundInterface;
use Laracatch\Client\DefinedErrors\ViewMissing;
use Throwable;

class DefinedErrorHandler implements DefinedErrorHandlerContract
{
    /** @var array */
    protected $definedErrors = [
        DatabaseAccessDeniedError::class,
        DatabaseMissingTable::class,
        DatabaseMissingField::class,
        EncryptionKeyNotSpecifiedError::class,
        ViewExceptionMissingVariable::class,
        ViewExceptionMissingQuoteClose::class,
        ViewMissing::class,
        RouteNameNotDefined::class,
        RouteMissingParameters::class,
        RouteMethodNotAllowed::class,
        RuntimeEncryptionUnsupported::class,
        UnboundInterface::class,
    ];

    /** @var Application */
    protected $app;

    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    /**
     * Push a new defined error to the list.
     *
     * @param DefinedErrorContract $definedError
     *
     * @return void
     */
    public function push(DefinedErrorContract $definedError): void
    {
        $this->definedErrors[] = $definedError;
    }

    /**
     * Resolve the available solutions for the error.
     *
     * @param Throwable $error
     *
     * @return array|mixed
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function resolveSolutions(Throwable $error): array
    {
        $solutions = [];

        foreach ($this->definedErrors as $definedError) {
            $resolvedError = $this->resolve($definedError);

            if ($resolvedError->match($error)) {
                $errorSolutions = $resolvedError->getSolutions($error);

                /** @var $solution SolutionContract */
                foreach ($errorSolutions as $solution) {
                    $solutions[] = $solution->toArray();
                }
            }
        }

        return array_filter($solutions);
    }

    /**
     * Resolve an instance of the given error.
     *
     * @param string $definedError
     *
     * @return DefinedErrorContract
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    protected function resolve(string $definedError): DefinedErrorContract
    {
        return $this->app->make($definedError);
    }
}
