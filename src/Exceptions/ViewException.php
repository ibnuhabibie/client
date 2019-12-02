<?php

namespace Laracatch\Client\Exceptions;

use ErrorException;
use Laracatch\Client\Collectors\Dumps\HtmlDumper;

class ViewException extends ErrorException
{
    /** @var string */
    protected $path = '';

    /** @var array */
    protected $data = [];

    public function setPath(string $path): void
    {
        $this->path = $path;
    }

    public function setData(array $data): void
    {
        $this->data = $data;
    }

    public function getData(): array
    {
        return $this->data;
    }

    public function getDumpData(): array
    {
        $dumper = new HtmlDumper;

        return [
            'view' => $this->path,
            'data' => array_map(static function ($var) use ($dumper) {
                return $dumper->dumpSingleVariable($var);
            }, $this->data)
        ];
    }
}