<?php

namespace Laracatch\Client\Tests\Actions;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User;
use Illuminate\Support\Arr;
use Laracatch\Client\Actions\ShareErrorAction;
use Laracatch\Client\Client\Client;
use Laracatch\Client\Collectors\Breadcrumbs\Breadcrumb;
use Laracatch\Client\Tests\TestCase;
use Psr\Log\LogLevel;

class ShareErrorActionTest extends TestCase
{
    protected $fakeClient;

    /** @var ShareErrorAction */
    protected $shareAction;

    protected $defaultTabs = [
        'stackTraceTab',
        'debugTab',
        'userTab',
        'queryTab',
        'requestTab',
        'appTab',
        'contextTab',
    ];

    public function setUp(): void
    {
        parent::setUp();

        $this->fakeClient = $this->mockClient();

        $this->shareAction = new ShareErrorAction($this->fakeClient);
    }

    /** @test */
    public function sharing_all_tabs_removes_no_data()
    {
        $error = $this->getTestError();

        $this->shareAction->handle($error, $this->removeTabs([]));

        $sharedError = $this->fakeClient->requests[0]['arguments']['error'];

        $this->assertEquals($error, $sharedError);
    }

    /** @test */
    public function it_removes_user_data()
    {
        $error = $this->getTestError();

        $this->shareAction->handle($error, $this->removeTabs(['userTab']));

        $sharedError = $this->fakeClient->requests[0]['arguments']['error'];

        $this->assertFalse(Arr::has($sharedError, 'context.user'));
        $this->assertFalse(Arr::has($sharedError, 'context.request.ip'));
        $this->assertFalse(Arr::has($sharedError, 'context.request.useragent'));
    }

    /** @test */
    public function it_removes_stack_frames_except_the_last_frame()
    {
        $error = $this->getTestError();

        $this->shareAction->handle($error, $this->removeTabs(['stackTraceTab']));

        $sharedError = $this->fakeClient->requests[0]['arguments']['error'];

        $this->assertCount(1, $sharedError['stacktrace']);
        $this->assertSame($error['stacktrace'][0], $sharedError['stacktrace'][0]);
    }

    /** @test */
    public function it_removes_debug_data()
    {
        $error = $this->getTestError();

        $this->shareAction->handle($error, $this->removeTabs(['debugTab']));

        $sharedError = $this->fakeClient->requests[0]['arguments']['error'];

        $this->assertCount(0, Arr::get($sharedError, 'breadcrumbs'));
        $this->assertFalse(Arr::has($sharedError, 'context.dumps'));
        $this->assertFalse(Arr::has($sharedError, 'context.logs'));
        $this->assertFalse(Arr::has($sharedError, 'context.events'));
    }

    /** @test */
    public function it_removes_queries_data()
    {
        $error = $this->getTestError();

        $this->shareAction->handle($error, $this->removeTabs(['queryTab']));

        $sharedError = $this->fakeClient->requests[0]['arguments']['error'];

        $this->assertFalse(Arr::has($sharedError, 'context.queries'));
    }

    /** @test */
    public function it_removes_request_data()
    {
        $error = $this->getTestError();

        $this->shareAction->handle($error, $this->removeTabs(['requestTab']));

        $sharedError = $this->fakeClient->requests[0]['arguments']['error'];

        $this->assertFalse(Arr::has($sharedError, 'context.request'));
        $this->assertFalse(Arr::has($sharedError, 'context.request_data'));
        $this->assertFalse(Arr::has($sharedError, 'context.headers'));
        $this->assertFalse(Arr::has($sharedError, 'context.cookies'));
        $this->assertFalse(Arr::has($sharedError, 'context.session'));
    }

    /** @test */
    public function it_removes_context_data()
    {
        $error = $this->getTestError();

        $this->shareAction->handle($error, $this->removeTabs(['contextTab']));

        $sharedError = $this->fakeClient->requests[0]['arguments']['error'];

        $this->assertFalse(Arr::has($sharedError, 'env'));
        $this->assertFalse(Arr::has($sharedError, 'git'));
        $this->assertFalse(Arr::has($sharedError, 'context.framework'));
    }

    /** @test */
    public function it_removes_app_data()
    {
        $error = $this->getTestError();

        $this->shareAction->handle($error, $this->removeTabs(['appTab']));

        $sharedError = $this->fakeClient->requests[0]['arguments']['error'];

        $this->assertFalse(Arr::has($sharedError, 'context.view'));
        $this->assertFalse(Arr::has($sharedError, 'context.route'));
    }

    protected function removeTabs(array $tabs): array
    {
        return array_diff($this->defaultTabs, $tabs);
    }

    protected function getTestError(): array
    {
        Model::unguard();

        $handler = $this->app->make('laracatch.handler');

        $error = $handler->buildFromThrowable(new \BadMethodCallException('Test Exception'));

        $error->setPerformanceMemory(['key' => 'value']);
        $error->setPerformanceTime(['key' => 'value']);
        $error->setContextEvents(['key' => 'value']);
        $error->setContextLogs(['key' => 'value']);
        $error->setContextQueries(['key' => 'value']);

        $error->setBreadcrumbs([
            new Breadcrumb('First Breadcrumb', LogLevel::INFO),
            new Breadcrumb('Second Breadcrumb', LogLevel::WARNING)
        ]);

        $error->setContextDumps(['key' => 'value']);
        $error->setContextSession(['key' => 'value']);
        $error->setContextHeaders(['key' => 'value']);
        $error->setContextCookies(['key' => 'value']);
        $error->setContextRoute(['key' => 'value']);

        $userData = (new User([
            'id' => 1,
            'name' => 'Taylor',
            'email' => 'taylor@laravel.com',
        ]))->toArray();

        $error->setContextUser($userData);

        $error->setContextRequest(
            'http://localhost',
            'GET',
            '127.0.0.1',
            'some-useragent-string'
        );

        $error->setContextRequestData(
            ['key' => 'value'],
            'body',
            ['file-key' => 'file-value']
        );

        $error->setContextFrameworkData(
            'Laravel 6.0.3',
            'en',
            false,
            '7.2.24-1+ubuntu18.04.1+deb.sury.org+1'
        );

        $error->setExceptionClass(self::class);

        $error->setEnvironmentAttributes('testing', app_path(), false);
        $error->setLocationAttributes('http://localhost', 'GET');

        return $error->toArray();
    }

    protected function mockClient()
    {
        $baseUrl = '';
        $timeout = 10;

        return new class($baseUrl, $timeout) extends Client
        {
            public $requests = [];

            public function post(string $url, array $arguments = [])
            {
                $this->requests[] = [
                    'url' => $url,
                    'arguments' => $arguments
                ];
            }
        };
    }
}
