<?php

namespace Laracatch\Client\Collectors\Git;

use Laracatch\Client\Contracts\GitCollectorContract;
use Illuminate\Contracts\Foundation\Application;

class GitCollector implements GitCollectorContract
{
    /** @var Application */
    protected $app;

    /** @var array */
    protected $gitInformation = [
        'isInitialized' => null,
        'hash' => null,
        'message' => null,
        'tag' => null,
        'remote' => null,
        'isDirty' => null
    ];

    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    /**
     * Collect git data.
     *
     * @return void
     */
    public function collect(): void
    {
        $this->gitInformation = [
            'isInitialized' => $this->isInitialized(),
            'hash' => $this->getHash(),
            'message' => $this->getMessage(),
            'tag' => $this->getTag(),
            'remote' => $this->getRemote(),
            'isDirty' => $this->getIsDirty()
        ];
    }

    /**
     * Get git data.
     *
     * @return array
     */
    public function getItems(): array
    {
        return $this->gitInformation;
    }

    protected function isInitialized(): bool
    {
        return (new Command(Commands::INITIALIZED))->run();
    }

    protected function getHash(): ?string
    {
        return (new Command(Commands::HASH))->run();
    }

    protected function getMessage(): ?string
    {
        return (new Command(Commands::MESSAGE))->run();
    }

    protected function getTag(): ?string
    {
        return (new Command(Commands::TAG))->run();
    }

    protected function getRemote(): ?string
    {
        return (new Command(Commands::REMOTE))->run();
    }

    protected function getIsDirty(): bool
    {
        return ! empty((new Command(Commands::STATUS))->run());
    }
}