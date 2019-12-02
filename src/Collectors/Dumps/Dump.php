<?php

namespace Laracatch\Client\Collectors\Dumps;

use Symfony\Component\VarDumper\Cloner\Data;

class Dump
{
    /** @var string */
    protected $html;

    /** @var Data */
    protected $originalData;

    /** @var string|null */
    protected $file;

    /** @var int|null */
    protected $line;

    /** @var float|null */
    protected $microtime;

    public function __construct(string $html, Data $originalData, ?string $file, ?int $line, ?float $microtime = null)
    {
        $this->html = $html;
        $this->originalData = $originalData;
        $this->file = $file;
        $this->line = $line;
        $this->microtime = $microtime ?? microtime(true);
    }

    /**
     * Return the dump as an array.
     *
     * @return array
     */
    public function toArray(): array
    {
        return [
            'html_dump' => $this->html,
            'original_data' => $this->originalData,
            'file' => $this->file,
            'line_number' => $this->line,
            'microtime' => $this->microtime
        ];
    }
}