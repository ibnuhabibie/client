<?php

namespace Laracatch\Client;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Arr;

class LaracatchConfig implements Arrayable
{
    /** @var array */
    protected $options;

    public function __construct(array $options = [])
    {
        $this->options = $this->mergeWithDefaultConfig($options);
    }

    protected function mergeWithDefaultConfig(array $options = []): array
    {
        return array_merge(config()->get('laracatch') ?: include __DIR__ . '/../config/laracatch.php', $options);
    }

    public function toArray(): array
    {
        return [
            'codeEditor' => $this->getCodeEditor(),
            'remoteFilePath' => $this->getRemoteFilePath(),
            'localFilePath' => $this->getLocalFilePath(),
            'navigator' => $this->getNavigator(),
            'theme' => $this->getTheme(),
            'sharing' => $this->getSharing(),
            'routePrefix' => $this->getRoutePrefix(),
            'directorySeparator' => DIRECTORY_SEPARATOR,
        ];
    }

    public function getCodeEditor(): ?string
    {
        return Arr::get($this->options, 'code_editor');
    }

    public function getRemoteFilePath(): ?string
    {
        return Arr::get($this->options, 'file_paths.remote');
    }

    public function getLocalFilePath(): ?string
    {
        return Arr::get($this->options, 'file_paths.local');
    }

    public function getNavigator(): bool
    {
        return Arr::get($this->options, 'storage.enabled');
    }

    public function getTheme(): ?string
    {
        return Arr::get($this->options, 'theme');
    }

    public function getSharing(): bool
    {
        return Arr::get($this->options, 'sharing', true);
    }

    public function getRoutePrefix(): string
    {
        return Arr::get($this->options, 'route_prefix', '_laracatch');
    }
}
