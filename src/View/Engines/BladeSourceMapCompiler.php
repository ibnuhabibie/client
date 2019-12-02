<?php

namespace Laracatch\Client\View\Engines;

use Illuminate\View\Compilers\BladeCompiler;

class BladeSourceMapCompiler extends BladeCompiler
{
    /**
     * Compile the given Blade template contents.
     *
     * @param  string $value
     * @return string
     */
    public function compileString($value): string
    {
        $value = $this->enhanceEchos($value);

        $value = $this->enhanceStatements($value);

        $value = parent::compileString($value);

        return $this->trimEmptyLines($value);
    }

    /**
     * Add line indications before echo instructions.
     *
     * @param string $value
     * @return string
     * @see CompilesEchos
     */
    protected function enhanceEchos(string $value)
    {
        $pattern = sprintf('/(@)?%s\s*(.+?)\s*%s(\r?\n)?/s', $this->contentTags[0], $this->contentTags[1]);

        if (preg_match_all($pattern, $value, $matches, PREG_OFFSET_CAPTURE))
        {
            foreach (array_reverse($matches[0]) as $match)
            {
                $value = $this->markLineNumberAtPosition($match[1], $value);
            }
        }

        return $value;
    }

    /**
     * Add line indications before statements.
     *
     * @param string $value
     * @return string
     * @see BladeCompiler::compileStatements()
     */
    protected function enhanceStatements(string $value)
    {
        $shouldInsertLineNumbers = preg_match_all(
            '/\B@(@?\w+(?:::\w+)?)([ \t]*)(\( ( (?>[^()]+) | (?3) )* \))?/x',
            $value,
            $matches,
            PREG_OFFSET_CAPTURE
        );

        if ($shouldInsertLineNumbers)
        {
            foreach (array_reverse($matches[0]) as $match)
            {
                $value = $this->markLineNumberAtPosition($match[1], $value);
            }
        }

        return $value;
    }

    /**
     * Detect the original exception line number
     * from the original Blade view and the exception
     * line number coming from the compiled view.
     *
     * @param string $filename
     * @param int $exceptionLineNumber
     * @return int
     */
    public function detectExceptionLineNumber(string $filename, int $exceptionLineNumber): int
    {
        $map = $this->compileString(file_get_contents($filename));
        $map = explode("\n", $map);

        $line = $map[$exceptionLineNumber - $this->getExceptionLineOffset()] ?? $exceptionLineNumber;

        if (preg_match('/\|---LINE:([0-9]+)---\|/m', $line, $matches))
        {
            return $matches[1];
        }

        return $exceptionLineNumber;
    }

    /**
     * Mark specific substring with the line number indication.
     *
     * @param int $position
     * @param string $value
     * @return string
     */
    protected function markLineNumberAtPosition(int $position, string $value)
    {
        $before = mb_substr($value, 0, $position);
        $lineNumber = count(explode("\n", $before));

        return mb_substr($value, 0, $position)."|---LINE:{$lineNumber}---|".mb_substr($value, $position);
    }

    /**
     * Laravel 5.8.0 - 5.8.9 added the view name as
     * a comment in the compiled view on a new line.
     *
     * @return int
     */
    protected function getExceptionLineOffset(): int
    {
        if (
            version_compare(app()->version(), '5.8.0', '>=')
            and
            version_compare(app()->version(), '5.8.9', '<=')
        )
        {
            return 2;
        }

        return 1;
    }

    protected function trimEmptyLines(string $value)
    {
        $value = preg_replace('/^\|---LINE:([0-9]+)---\|$/m', '', $value);

        return ltrim($value, PHP_EOL);
    }
}
