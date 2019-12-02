<?php

namespace Laracatch\Client\Tests\DataProviders;

use Laracatch\Client\DataProviders\ViewDataProvider;
use Laracatch\Client\Exceptions\ViewException;
use Laracatch\Client\Models\ErrorModel;
use Laracatch\Client\Tests\TestCase;
use Throwable;

class ViewDataProviderTest extends TestCase
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
    public function it_should_give_view_data()
    {
        $throwable = new ViewException();
        $throwable->setData(['key' => 'value']);
        $errorModelArray = $this->callDataProvider($throwable);

        $this->assertArrayHasKey('data', $errorModelArray['context']['view']);
        $this->assertArrayHasKey('key', $errorModelArray['context']['view']['data']);
    }

    /** @test */
    public function view_data_should_not_be_set()
    {
        $this->app['config']->set('laracatch.data_providers.report_view_data', false);

        $throwable = new ViewException();
        $throwable->setData(['key' => 'value']);
        $errorModelArray = $this->callDataProvider($throwable);

        $this->assertNull($errorModelArray['context']['view']);
    }

    /**
     * @param Throwable $throwable
     * @return array
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    protected function callDataProvider(Throwable $throwable)
    {
        app()->make(ViewDataProvider::class)->handle($this->errorModel, $this->app, $throwable);

        return $this->errorModel->toArray();
    }
}