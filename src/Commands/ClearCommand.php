<?php

namespace Laracatch\Client\Commands;

use Illuminate\Console\Command;
use Laracatch\Client\Contracts\StorageContract;
use Laracatch\Client\Handler\Laracatch;

class ClearCommand extends Command
{
    /**
     * @var string
     */
    protected $name = 'laracatch:clear';

    /**
     * @var string
     */
    protected $description = 'Clear the Laracatch Storage from all the exceptions';

    /**
     * ClearCommand constructor.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Handle the command
     *
     * @param StorageContract $storage
     */
    public function handle(StorageContract $storage)
    {
        $storage->clear();

        $this->info('Storage cleared!');
    }
}
