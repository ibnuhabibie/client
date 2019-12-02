<?php

namespace Laracatch\Client\Collectors\Breadcrumbs;

use Psr\Log\LogLevel;

class Breadcrumb
{
    /** @var string */
    protected $message;

    /** @var string */
    protected $logLevel;

    /** @var array */
    protected $meta;

    /** @var float */
    protected $microtime;

    public function __construct(
        string $message,
        string $logLevel = LogLevel::INFO,
        array $meta = [],
        ?float $microtime = null
    ) {
        $this->message = $message;
        $this->logLevel = $logLevel;
        $this->meta = $meta;
        $this->microtime = $microtime ?? microtime(true);
    }

    /**
     * Convert the breadcrumb to an array.
     *
     * @return array
     */
    public function toArray(): array
    {
        return [
            'time' => now()->timestamp,
            'message' => $this->getMessage(),
            'log_level' => $this->getLogLevel(),
            'meta' => $this->getMeta(),
            'microtime' => $this->getMicrotime()
        ];
    }

    /**
     * @return string
     */
    public function getMessage(): string
    {
        return $this->message;
    }

    /**
     * @return string
     */
    public function getLogLevel(): string
    {
        return $this->logLevel;
    }

    /**
     * @return array
     */
    public function getMeta(): array
    {
        return $this->meta;
    }

    /**
     * @return float
     */
    public function getMicrotime(): float
    {
        return $this->microtime;
    }
}
