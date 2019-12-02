<?php

namespace Laracatch\Client\Tests\DataProviders;

use Illuminate\Database\QueryException;
use Illuminate\Support\Arr;
use Laracatch\Client\DataProviders\SolutionDataProvider;
use Laracatch\Client\Models\ErrorModel;
use Laracatch\Client\Tests\TestCase;
use RuntimeException;
use Throwable;

class SolutionDataProviderTest extends TestCase
{

    /**
     * @var ErrorModel
     */
    protected $errorModel;

    /**
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->errorModel = $this->makeErrorModel();
    }

    /** @test */
    public function it_should_give_database_access_denied_solution()
    {
        $throwable = new QueryException('Sql error', [], new \Exception(null, 1045));
        $errorModel = $this->callSolutionDataProvider($throwable);

        $this->assertSame('Database connection credentials seem to be invalid', Arr::get($errorModel, 'solutions.0.title'));
    }

    /** @test */
    public function it_should_give_encryption_key_command_solution()
    {
        $throwable = new RuntimeException('No application encryption key has been specified.');
        $errorModel = $this->callSolutionDataProvider($throwable);

        $this->assertSame('The `APP_KEY` configuration variable in the `.env` file is not specified', Arr::get($errorModel, 'solutions.0.title'));
    }

    /**
     * @param Throwable $throwable
     * @return array
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    protected function callSolutionDataProvider(Throwable $throwable)
    {
        app()->make(SolutionDataProvider::class)->handle($this->errorModel, $this->app, $throwable);

        return $this->errorModel->toArray();
    }
}