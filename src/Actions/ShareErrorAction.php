<?php

namespace Laracatch\Client\Actions;

use Exception;
use Illuminate\Support\Arr;
use Laracatch\Client\Client\Client;
use Laracatch\Client\Exceptions\ShareErrorException;

class ShareErrorAction
{
    /** @var array */
    protected $tabs;

    /** @var Client */
    protected $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * Handle the action.
     *
     * @param array $error
     * @param array $tabs
     * @param string|null $lineSelection
     *
     * @return array|false
     * @throws ShareErrorException
     */
    public function handle(array $error, array $tabs, ?string $lineSelection = null)
    {
        $filteredErrors = $this->applyErrorFilters($error, $tabs);

        try {
            return $this->client->post('share', [
                'error' => $filteredErrors,
                'tabs' => $tabs,
                'lineSelection' => $lineSelection,
            ]);
        } catch (Exception $exception) {
            throw new ShareErrorException($exception->getMessage());
        }
    }

    /**
     * Apply selected filters to the error before sharing.
     *
     * @param array $error
     * @param array $tabs
     *
     * @return array
     */
    protected function applyErrorFilters(array $error, array $tabs): array
    {
        if (! $this->includeTab('debugTab', $tabs)) {
            $error['breadcrumbs'] = [];
        }

        if (! $this->includeTab('stackTraceTab', $tabs)) {
            $error['stacktrace'] = array_slice($error['stacktrace'], 0, 1);
        }

        $error['context'] = $this->applyContextFilters($error['context'], $tabs);

        return $error;
    }

    /**
     * Check whether we should include the tab based on the supplied configuration.
     *
     * @param string $tab
     * @param array $tabs
     *
     * @return bool
     */
    protected function includeTab(string $tab, array $tabs): bool
    {
        return in_array($tab, $tabs, true);
    }

    /**
     * Apply filters to the error context data.
     *
     * @param array $errorContext
     * @param array $tabs
     *
     * @return array
     */
    protected function applyContextFilters(array $errorContext, array $tabs): array
    {
        if (! $this->includeTab('appTab', $tabs)) {
            Arr::forget($errorContext, ['view', 'route']);
        }

        if (! $this->includeTab('contextTab', $tabs)) {
            Arr::forget(
                $errorContext,
                array_merge(['env', 'git', 'context'], $this->getCustomContextKeys($errorContext))
            );
        }

        if (! $this->includeTab('debugTab', $tabs)) {
            Arr::forget($errorContext, ['dumps', 'breadcrumbs', 'logs', 'events']);
        }

        if (! $this->includeTab('queryTab', $tabs)) {
            Arr::forget($errorContext, 'queries');
        }

        if (! $this->includeTab('requestTab', $tabs)) {
            Arr::forget($errorContext, ['request', 'request_data', 'headers', 'session', 'cookies']);
        }

        if (! $this->includeTab('userTab', $tabs)) {
            Arr::forget($errorContext, ['user', 'request.ip', 'request.useragent']);
        }

        return $errorContext;
    }

    /**
     * Get the custom keys for the context so they can be filtered.
     *
     * @param array $errorContext
     *
     * @return array
     */
    protected function getCustomContextKeys(array $errorContext): array
    {
        $defaultContext = [
            'context',
            'cookies',
            'dumps',
            'env',
            'events',
            'git',
            'headers',
            'logs',
            'queries',
            'request',
            'request_data',
            'route',
            'session',
            'user',
            'view'
        ];

        return array_diff(array_keys($errorContext), $defaultContext);
    }
}
