<?php

namespace Laracatch\Client\Tests\Handler\Storage;

use Carbon\Carbon;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;
use Laracatch\Client\Storage\FilesystemStorage;
use Laracatch\Client\Tests\TestCase;

class FilesystemStorageTest extends TestCase
{
    protected $filesystem;

    protected $storage;

    protected $path = '__TEST__/';

    public function setUp(): void
    {
        parent::setUp();

        $this->filesystem = new Filesystem();

        $this->storage = new FilesystemStorage($this->filesystem, $this->path);
    }

    /** @test */
    public function it_should_create_the_filename_based_on_the_id()
    {
        $id = Str::random();

        $fullpath = $this->path . $id . '.json';

        $this->assertEquals($fullpath, $this->storage->makeFilename($id));
    }

    /** @test */
    public function it_should_create_the_directory_if_it_does_not_exist()
    {
        $this->assertFalse($this->filesystem->exists($this->path));

        $this->storage->save([
            'key' => 'value'
        ]);

        $this->assertTrue($this->filesystem->exists($this->path));
    }

    /** @test */
    public function it_should_store_a_new_file()
    {
        $id = $this->storage->save([
            'key' => 'value'
        ]);

        $this->assertTrue($this->filesystem->exists($this->path . $id . '.json'));
    }

    /** @test */
    public function it_should_add_a_gitignore_file_in_the_directory()
    {
        $this->storage->save([
            'key' => 'value'
        ]);

        $this->assertTrue($this->filesystem->exists($this->path . '.gitignore'));
    }

    /** @test */
    public function it_should_clear_files_older_than_the_retention_period_when_saving_a_new_file()
    {
        config()->set('laracatch.storage.retention', 24);

        $id = $this->storage->save([
            'key' => 'value'
        ]);

        $originalTimestamp = filemtime($this->path . $id . '.json');

        touch($this->path . $id . '.json', Carbon::now()->subDay()->subHour()->timestamp);

        clearstatcache();

        $modifiedTimestamp = filemtime($this->path . $id . '.json');

        $this->assertTrue($modifiedTimestamp < $originalTimestamp);

        $newId = $this->storage->save([
            'key' => 'value'
        ]);

        $this->assertTrue($this->filesystem->exists($this->path . $newId . '.json'));
        $this->assertFalse($this->filesystem->exists($this->path . $id . '.json'));
    }

    /** @test */
    public function it_should_return_the_content_of_a_file()
    {
        $id = $this->storage->save([
            'key' => 'value'
        ]);

        $this->assertEquals(['key' => 'value'], $this->storage->find($id));
    }

    /** @test */
    public function it_should_return_a_slice_of_the_total_results()
    {
        foreach (range(1, 5) as $i) {
            $id = $this->storage->save(['index' => $i]);

            $this->changeModifiedTime($this->path . $id . '.json', Carbon::now()->subMinutes(5 - $i)->timestamp);
        }

        $results = $this->storage->get([], 2);

        $this->assertArrayHasKey('id', $results[0]);
        $this->assertArraySubset(['index' => 5], $results[0]);
        $this->assertArrayHasKey('id', $results[1]);
        $this->assertArraySubset(['index' => 4], $results[1]);
    }

    /**
     * Change modified time of file.
     * This is necessary because files can be saved
     * at the exact same timestamp, jeopardizing results
     *
     * @param $file
     * @param $timestamp
     */
    protected function changeModifiedTime($file, $timestamp)
    {
        touch($file, $timestamp);
    }

    /** @test */
    public function it_should_filter_the_results_using_the_gte_operator()
    {
        foreach (range(1, 5) as $i) {
            $id = $this->storage->save(['index' => $i]);

            $this->changeModifiedTime($this->path . $id . '.json', Carbon::now()->subMinutes(5 - $i)->timestamp);
        }

        $results = $this->storage->get(['index.gte' => 4], 2);

        $this->assertArraySubset(['index' => 5], $results[0]);
        $this->assertArraySubset(['index' => 4], $results[1]);
    }

    /** @test */
    public function it_should_filter_the_results_using_the_eq_operator()
    {
        foreach (range(1, 5) as $i) {
            $this->storage->save(['index' => $i]);
        }

        $results = $this->storage->get(['index.eq' => 3]);

        $this->assertCount(1, $results);
        $this->assertArraySubset(['index' => 3], $results[0]);
    }

    /** @test */
    public function it_should_filter_the_results_using_the_lte_operator()
    {
        foreach (range(1, 5) as $i) {
            $id = $this->storage->save(['index' => $i]);

            $this->changeModifiedTime($this->path . $id . '.json', Carbon::now()->subHours(5 - $i)->timestamp);
        }

        $results = $this->storage->get(['index.lte' => 2]);

        $this->assertArraySubset(['index' => 2], $results[0]);
        $this->assertArraySubset(['index' => 1], $results[1]);
    }

    /** @test */
    public function it_should_filter_the_results_using_the_neq_operator()
    {
        foreach (range(1, 5) as $i) {
            $id = $this->storage->save(['index' => $i]);

            $this->changeModifiedTime($this->path . $id . '.json', Carbon::now()->subMinutes(5 - $i)->timestamp);
        }

        $results = $this->storage->get(['index.neq' => 3]);

        $this->assertCount(4, $results);

        $this->assertArraySubset(['index' => 5], $results[0]);
        $this->assertArraySubset(['index' => 4], $results[1]);
        $this->assertArraySubset(['index' => 2], $results[2]);
        $this->assertArraySubset(['index' => 1], $results[3]);
    }

    /** @test */
    public function it_should_clear_the_storage_path()
    {
        $this->storage->save([
            'key' => 'value'
        ]);

        $this->storage->clear();

        $this->assertEmpty($this->filesystem->allFiles($this->path));
    }

    protected function tearDown(): void
    {
        $this->filesystem->deleteDirectory($this->path);

        parent::tearDown();
    }
}
