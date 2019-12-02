<?php

namespace Laracatch\Client\Support;

use Illuminate\Contracts\Foundation\Application;
use SplFileObject;
use Throwable;

class Stacktrace
{
    use AttributeTypeSerializationTrait;

    /** @var string */
    protected $projectRoot;

    /** @var string|null */
    protected $localRoot;

    /** @var int */
    protected $stackTraceLimit = 100;

    /** @var array */
    protected $laracatchFiles = [
        '/laracatch/src/helpers.php',
    ];

    public function __construct(string $projectRoot, ?string $localPath = null)
    {
        $this->projectRoot = $projectRoot;
        $this->localRoot = $localPath;
    }

    /**
     * Create a new stack trace from the given throwable.
     *
     * @param Throwable $throwable
     * @param Application $app
     *
     * @return array
     */
    public static function createFromThrowable(Throwable $throwable, Application $app): array
    {
        $localRoot = $app['config']->get('laracatch.file_paths.local');

        return (new static($app->basePath(), $localRoot))->stackTraceToArray(
            $throwable->getTrace(),
            $throwable->getFile(),
            $throwable->getLine()
        );
    }

    /**
     * Create a new stacktrace.
     *
     * @param Application $app
     *
     * @return array
     */
    public static function create(Application $app): array
    {
        $localRoot = $app['config']->get('laracatch.file_paths.local');

        return (new static($app->basePath(), $localRoot))->getCurrentStackTrace();
    }

    /**
     * Serialize a stack trace to an array.
     *
     * @param array $stackTrace
     * @param string|null $topFile
     * @param int|null $topLine
     *
     * @return array
     */
    public function stackTraceToArray(array $stackTrace, ?string $topFile = null, ?int $topLine = null): array
    {
        $stack = [];
        $counter = 0;

        $frameFile = $topFile;
        $frameLine = $topLine;

        foreach ($stackTrace as $frame) {
            if (! $this->shouldIgnoreFile($frameFile)) {
                $type = $this->stackTrackTypeToString($frame);
                $args = $this->stackTraceArgsToArray($frame);

                $stack[] = [
                    'class' => $frame['class'] ?? null,
                    'method' => $frame['function'] ?? null,
                    'args' => $args,
                    'type' => $type,
                    'file' => $this->getStackTraceFile($frameFile),
                    'relative_file' => $this->getStackTraceRelativeFile($frameFile),
                    'line_number' => $frameLine,
                    'code_snippet' => $this->getCode($this->getStackTraceFile($frameFile), $frameLine),
                ];

                $counter++;
            }

            // The file/line and class in the trace seem to be misaligned and have to be set to be used the next time
            // around, rather than used directly in a given stack item.
            $frameFile = $frame['file'] ?? 'unknown';
            $frameLine = $frame['line'] ?? 0;

            if ($counter >= $this->stackTraceLimit) {
                break;
            }
        }

        $stack[] = [
            'class' => null,
            'method' =>'[top]',
            'args' => [],
            'type' => null,
            'file' => $frameFile,
            'relative_file' => $this->getStackTraceRelativeFile($frameFile),
            'line_number' => $frameLine,
            'code_snippet' => $this->getCode($this->getStackTraceFile($frameFile), $frameLine),
        ];

        return $stack;
    }

    /**
     * Determine if a file should be ignored
     *
     * @param string|null $currentFile
     * @return bool
     */
    protected function shouldIgnoreFile(?string $currentFile): bool
    {
        if (! $currentFile) {
            return true;
        }

        $currentFile =  str_replace('\\', '/', $currentFile);

        foreach ($this->laracatchFiles as $ignoredFile) {
            if (strpos($currentFile, $ignoredFile) !== false) {
                return true;
            }
        }
        return false;
    }

    /**
     * @return array
     */
    public function getCurrentStackTrace(): array
    {
        return $this->resolveCurrentStackTrace();
    }

    /**
     * Stringify the stack trace call type.
     *
     * @param array $trace
     *
     * @return string
     */
    protected function stackTrackTypeToString(array $trace): string
    {
        if (! isset($trace['type'])) {
            return 'function';
        }

        if ($trace['type'] === '::') {
            return 'static';
        }

        if ($trace['type'] === '->') {
            return 'method';
        }

        return 'unknown';
    }

    /**
     * Serialize stack trace function arguments.
     *
     * @param array $trace
     *
     * @return array
     */
    protected function stackTraceArgsToArray(array $trace): array
    {
        $params = [];

        if (! isset($trace['args'])) {
            return $params;
        }

        foreach ($trace['args'] as $arg) {
            $params[] = $this->serializeValue($arg);
        }

        return $params;
    }

    /**
     * Return file on the local system for the stack trace.
     *
     * @param string|null $file
     *
     * @return string|null
     */
    protected function getStackTraceFile(?string $file = null): ?string
    {
        if (! $file) {
            return null;
        }

        if (! $this->localRoot) {
            return $file;
        }

        $rootlessPath = $this->removeProjectRoot($file);

        return rtrim($this->localRoot, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . ltrim($rootlessPath, DIRECTORY_SEPARATOR);
    }

    /**
     * Return the relative path for the given file.
     *
     * @param string|null $file
     *
     * @return string|null
     */
    protected function getStackTraceRelativeFile(?string $file): ?string
    {
        if (! $file) {
            return null;
        }

        return ltrim($this->removeProjectRoot($file), DIRECTORY_SEPARATOR);
    }

    /**
     * Remove the project route from the given path.
     *
     * @param string $path
     *
     * @return string
     */
    protected function removeProjectRoot(string $path): string
    {
        if (strpos($path, $this->projectRoot) === 0) {
            return substr($path, strlen($this->projectRoot));
        }

        return $path;
    }

    /**
     * @param string|null $path
     * @param int|null $line
     * @param int $linesAround
     * @return array|void
     */
    public function getCode(string $path = null, int $line = null, $linesAround = 10)
    {
        if (! $path || ! $line) {
            return;
        }

        try {
            $file = new SplFileObject($path);
            $file->setMaxLineLen(250);
            $file->seek(PHP_INT_MAX);
            $codeLines = [];

            $from = max(0, $line - $linesAround - 1);
            $to = min($line + $linesAround - 1, $file->key() + 1);

            $file->seek($from);

            while ($file->key() <= $to && ! $file->eof()) {
                // `key()` returns 0 as the first line
                $codeLines[$file->key()+1] = rtrim($file->current());

                $file->next();
            }

            return $codeLines;
        } catch (Throwable $e) {
            return [];
        }
    }

    /**
     * Get the current stack trace.
     *
     * @return array
     */
    protected function resolveCurrentStackTrace(): array
    {
        $stackTrace = debug_backtrace(DEBUG_BACKTRACE_PROVIDE_OBJECT, $this->stackTraceLimit);

        foreach ($stackTrace as $index => $trace) {
            if (isset($trace['function']) && $trace['function'] === 'ddd') {
                break;
            }

            unset($stackTrace[$index]);
        }

        return $this->stackTraceToArray($stackTrace);
    }
}
