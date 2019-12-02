<?php

namespace Laracatch\Client\Tests;

use Illuminate\Filesystem\Filesystem;

trait FilesystemInteraction
{
    protected $path = '__TEST__/';

    protected $filesystem;

    public function setUp(): void
    {
        parent::setUp();

        $this->filesystem = new Filesystem();

        file_exists($this->path) ?: $this->filesystem->makeDirectory($this->path);
    }

    public function tearDown(): void
    {
        parent::tearDown();

        $this->filesystem->deleteDirectory($this->path);
    }

    private function mockFile($lineCount): string
    {
        $path = $this->path . "$lineCount-lines.text";

        $this->filesystem->put(
            $path,
            implode("\n", array_map(function ($n) {
                return 'Line ' . $n;
            }, range(1, $lineCount)))
        );

        return $path;
    }

    private function mockLongFile($content): string
    {
        $path = $this->path . 'long-line.txt';

        $this->filesystem->put($path, $content);

        return $path;
    }
}
