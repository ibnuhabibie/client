<?php

namespace Laracatch\Client;

use Error;
use ErrorException;
use Whoops\Handler\Handler;

class LaracatchWhoopsHandler extends Handler
{
    /**
     * @param LaracatchHandler $laracatch
     */
    public function __construct(LaracatchHandler $laracatch)
    {
        $this->laracatch = $laracatch;
    }

    /**
     * @return int|null
     * @throws ErrorException
     */
    public function handle(): ?int
    {
        $throwable = $this->getException();

        $errorModel = $this->laracatch->buildFromThrowable($throwable);

        try {
            echo $this->laracatch->getHtmlResponse($errorModel);
        } catch (Error $error) {
            // Errors aren't caught by Whoops.
            // Convert the error to an exception and throw again.

            throw new ErrorException(
                $error->getMessage(),
                $error->getCode(),
                1,
                $error->getFile(),
                $error->getLine(),
                $error
            );
        }

        return Handler::QUIT;
    }
}
