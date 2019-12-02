<?php

namespace Laracatch\Client\Collectors\Git;

use InvalidArgumentException;
use ReflectionClass;
use ReflectionException;
use Symfony\Component\Process\Process;

class Command
{
    /** @var Process */
    protected $process;

    /**
     * Command constructor.
     *
     * @param string $command
     *
     * @throws ReflectionException
     */
    public function __construct(string $command)
    {
        if (! in_array($command, Commands::$enumerated, true)) {
            throw new InvalidArgumentException('You must use one of the available git commands.');
        }

        $this->process = $this->getProcess($command);
    }

    /**
     * Run the command.
     *
     * @return string|null
     */
    public function run(): ?string
    {
        $this->process->run();

        return trim($this->process->getOutput());
    }

    /**
     * Get the process instance used to run the command.
     *
     * @param string $command
     *
     * @return Process
     * @throws ReflectionException
     */
    protected function getProcess(string $command): Process
    {
        $reflected = new ReflectionClass(Process::class);

        if ($reflected->hasMethod('fromShellCommandline')) {
            return Process::fromShellCommandline($command, base_path());
        }

        return new Process([$command], base_path());
    }
}
