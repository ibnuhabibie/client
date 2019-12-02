<?php

namespace Laracatch\Client\Tests\Handler\Storage;

use Carbon\Carbon;
use Laracatch\Client\Contracts\StorageContract;
use Laracatch\Client\Tests\TestCase;

class PdoStorageTest extends TestCase
{
    protected $storage;

    public function setUp(): void
    {
        parent::setUp();

        $this->loadMigrationsFrom(realpath(__DIR__ . '/../../migrations'));

        config()->set('laracatch.storage.driver', 'pdo');
        config()->set('laracatch.storage.connection', 'testbench');

        // Forget singleton instance and recreate
        $this->app->forgetInstance(StorageContract::class);

        $this->storage = $this->app->make(StorageContract::class);
    }

    /** @test */
    public function it_should_store_a_new_entry()
    {
        $this->storage->save($this->seedEntry());

        $this->assertEquals(1, \DB::table('laracatch')->count());
    }

    protected function seedEntry($override = [])
    {
        return array_merge([
            'seen_at' => microtime(true),
            'location' => 'http://localhost',
            'console' => false,
            'ip' => '1.2.3.4',
            'method' => 'GET'
        ], $override);
    }

    /** @test */
    public function it_should_clear_entires_older_than_the_retention_period_when_saving_a_new_entry()
    {
        config()->set('laracatch.storage.retention', 24);

        $this->storage->save($this->seedEntry([
            'seen_at' => Carbon::now()->subDay()->subHour()->timestamp
        ]));

        $newId = $this->storage->save($this->seedEntry());

        $this->assertEquals(1, \DB::table('laracatch')->count());
        $this->assertEquals($newId, \DB::table('laracatch')->first('id')->id);
    }

    /** @test */
    public function it_should_find_an_entry()
    {
        $id = $this->storage->save($this->seedEntry());

        $row = $this->storage->find($id);

        $this->assertEquals('http://localhost', $row['location']);
        $this->assertEquals(false, $row['console']);
        $this->assertEquals('1.2.3.4', $row['ip']);
        $this->assertEquals('GET', $row['method']);
    }

    /** @test */
    public function it_should_return_a_slice_of_the_total_results()
    {
        foreach (range(1, 5) as $i) {
            $this->storage->save($this->seedEntry([
                'seen_at' => Carbon::now()->subMinutes(5 - $i)->timestamp,
                'ip' => $i
            ]));
        }

        $rows = $this->storage->get([], 2);

        $this->assertCount(2, $rows);
        $this->assertEquals(5, $rows[0]['ip']);
        $this->assertEquals(4, $rows[1]['ip']);
    }

    /** @test */
    public function it_should_filter_the_results_using_the_gte_operator()
    {
        foreach (range(1, 5) as $i) {
            $this->storage->save($this->seedEntry([
                'seen_at' => Carbon::now()->subMinutes(5 - $i)->timestamp,
                'ip' => $i
            ]));
        }

        $rows = $this->storage->get(['ip.gte' => 4], 2);

        $this->assertCount(2, $rows);
        $this->assertEquals(5, $rows[0]['ip']);
        $this->assertEquals(4, $rows[1]['ip']);
    }

    /** @test */
    public function it_should_filter_the_results_using_the_eq_operator()
    {
        foreach (range(1, 5) as $i) {
            $this->storage->save($this->seedEntry([
                'ip' => $i
            ]));
        }

        $rows = $this->storage->get(['ip.eq' => 3]);

        $this->assertEquals(3, $rows[0]['ip']);
    }

    /** @test */
    public function it_should_filter_the_results_using_the_lte_operator()
    {
        foreach (range(1, 5) as $i) {
            $this->storage->save($this->seedEntry([
                'seen_at' => Carbon::now()->subMinutes(5 - $i)->timestamp,
                'ip' => $i
            ]));
        }

        $rows = $this->storage->get(['ip.lte' => 2]);

        $this->assertCount(2, $rows);
        $this->assertEquals(2, $rows[0]['ip']);
        $this->assertEquals(1, $rows[1]['ip']);
    }

    /** @test */
    public function it_should_filter_the_results_using_the_neq_operator()
    {
        foreach (range(1, 5) as $i) {
            $this->storage->save($this->seedEntry([
                'seen_at' => Carbon::now()->subMinutes(5 - $i)->timestamp,
                'ip' => $i
            ]));
        }

        $rows = $this->storage->get(['ip.neq' => 3]);

        $this->assertCount(4, $rows);
        $this->assertEquals(5, $rows[0]['ip']);
        $this->assertEquals(4, $rows[1]['ip']);
        $this->assertEquals(2, $rows[2]['ip']);
        $this->assertEquals(1, $rows[3]['ip']);
    }

    /** @test */
    public function it_should_clear_the_storage_table()
    {
        foreach (range(1, 5) as $i) {
            $this->storage->save($this->seedEntry([
                'ip' => $i
            ]));
        }

        $this->storage->clear();

        $this->assertEquals(0, \DB::table('laracatch')->count());
    }

    /**
     * Define environment setup.
     *
     * @param \Illuminate\Foundation\Application $app
     *
     * @return void
     */
    protected function getEnvironmentSetUp($app)
    {
        // Setup default database to use sqlite :memory:
        $app['config']->set('database.default', 'testbench');
        $app['config']->set('database.connections.testbench', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);
    }
}
