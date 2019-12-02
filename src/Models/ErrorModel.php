<?php

namespace Laracatch\Client\Models;

use Illuminate\Contracts\Support\Arrayable;
use Laracatch\Client\Exceptions\ViewException;
use Laracatch\Client\Support\AttributeAccessTrait;

class ErrorModel implements Arrayable
{
    use AttributeAccessTrait;

    /** @var array */
    protected $data = [
        'exception_class' => null,

        'seen_at' => null,
        'message' => null,

        'application_path' => null,
        'environment' => null,
        'console' => null,

        'location' => null,
        'method' => null,

        'breadcrumbs' => [],
        'solutions' => [],
        'performance' => [],

        'context' => [
            'request' => null,
            'request_data' => null,
            'headers' => null,
            'cookies' => null,
            'session' => null,
            'route' => null,
            'user' => null,
            'logs' => null,
            'queries' => null,
            'events' => null,
            'dumps' => null,
            'framework' => null,
            'view' => null,
            'git' => null
        ],
        'stacktrace' => [],
    ];

    public function __construct($message, $seenAt)
    {
        $this->data['message'] = $message;
        $this->data['seen_at'] = round($seenAt / 1000);
        $this->data['seen_at_microseconds'] = $seenAt;
    }

    public function setPerformanceMemory(array $memory): void
    {
        $this->data['performance']['memory'] = $memory;
    }

    public function setPerformanceTime(array $time): void
    {
        $this->data['performance']['time'] = $time;
    }

    public function setContextEvents(array $events): void
    {
        $this->data['context']['events'] = $events;
    }

    public function setContextLogs(array $logs): void
    {
        $this->data['context']['logs'] = $logs;
    }

    public function setContextQueries(array $queries): void
    {
        $this->data['context']['queries'] = $queries;
    }

    public function setBreadcrumbs(array $breadcrumbs): void
    {
        foreach ($breadcrumbs as $breadcrumb) {
            $this->data['breadcrumbs'][] = $breadcrumb->toArray();
        }
    }

    public function setContextDumps(array $dumps): void
    {
        $this->data['context']['dumps'] = $dumps;
    }

    public function setContextSession(array $sessionData): void
    {
        $this->data['context']['session'] = $sessionData;
    }

    public function setContextHeaders(array $headers): void
    {
        $this->data['context']['headers'] = $headers;
    }

    public function setContextCookies(array $cookies): void
    {
        $this->data['context']['cookies'] = $cookies;
    }

    public function setContextRoute(array $route): void
    {
        $this->data['context']['route'] = $route;
    }

    public function setContextUser(array $user): void
    {
        $this->data['context']['user'] = $user;
    }

    public function setContextRequest(
        string $url,
        string $httpMethod,
        ?string $ip = null,
        ?string $userAgent = null
    ): void {
        $this->data['context']['request'] = [
            'url' => $url,
            'method' => $httpMethod,
            'ip' => $ip,
            'useragent' => $userAgent
        ];
    }

    public function setContextRequestData(array $queryString, $body, array $files): void
    {
        $this->data['context']['request_data'] = [
            'query_string' => $queryString,
            'body' => $body,
            'files' => $files,
        ];
    }

    public function setContextFrameworkData(
        string $laravelVersion,
        string $laravelLocale,
        bool $laravelConfigCached,
        string $phpVersion
    ): void {
        $this->data['context']['framework'] = [
            'laravel_version' => $laravelVersion,
            'laravel_locale' => $laravelLocale,
            'laravel_config_cached' => $laravelConfigCached,
            'php_version' => $phpVersion
        ];
    }

    public function setContextView(ViewException $exception): void
    {
        $this->data['context']['view'] = $exception->getDumpData();
    }

    public function setContextGit(array $gitData): void
    {
        $this->data['context']['git'] = $gitData;
    }

    public function setExceptionClass(?string $className = null): void
    {
        $this->data['exception_class'] = $className;
    }

    public function setEnvironmentAttributes(string $env, string $appPath, bool $isConsole): void
    {
        $this->data['environment'] = $env;
        $this->data['application_path'] = $appPath;
        $this->data['console'] = $isConsole;
    }

    public function setLocationAttributes(?string $location = null, ?string $method = null): void
    {
        $this->data['location'] = $location;
        $this->data['method'] = $method;
    }

    public function setStacktrace(array $stacktrace): void
    {
        $this->data['stacktrace'] = $stacktrace;
    }

    public function setSolutions(array $solutions): void
    {
        $this->data['solutions'] = $solutions;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return $this->data;
    }

    /**
     * @return false|string
     */
    public function toJson()
    {
        return json_encode($this->data,
            JSON_PARTIAL_OUTPUT_ON_ERROR | JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT);
    }
}