<?php

namespace Laracatch\Client\Collectors\Dumps;

use Symfony\Component\VarDumper\Cloner\Data;
use Symfony\Component\VarDumper\Cloner\VarCloner;
use Symfony\Component\VarDumper\Dumper\HtmlDumper as BaseHtmlDumper;

class HtmlDumper extends BaseHtmlDumper
{
    protected const MAX_DEPTH = 3;
    protected const MAX_STRING_LENGTH = 160;

    /** @var string */
    protected $dumpHeader = '';

    /**
     * Dump an individual variable.
     *
     * @param mixed $var
     *
     * @return string
     */
    public function dumpSingleVariable($var): string
    {
        return $this->dump((new VarCloner())->cloneVar($var)->withMaxDepth(self::MAX_DEPTH));
    }

    /**
     * Dump the given data as HTML.
     *
     * @param Data $data
     * @param null $output
     * @param array $extraDisplayOptions
     *
     * @return string
     */
    public function dump(Data $data, $output = null, array $extraDisplayOptions = []): string
    {
        return parent::dump($data, true, [
            'maxDepth' => self::MAX_DEPTH,
            'maxStringLength' => self::MAX_STRING_LENGTH,
        ]);
    }
}