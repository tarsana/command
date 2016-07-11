<?php namespace Tarsana\Application;

use Tarsana\Application\Commands\Command;

/**
 * Commands runner; schedules and runs commands.
 */
class Runner {

    /**
     * List of commands to run.
     *
     * @var Tarsana\Application\Commands\Command[]
     */
    protected $commands;

    public function __construct ()
    {
        $this->commands = [];
    }

    /**
     * Schedules a command.
     *
     * @param  Command $command
     * @param  IO $io
     * @param  string|null  $input
     * @return self
     */
    public function schedule (Command $command, IO $io, $input = null)
    {
        $this->commands[] = [
            'cmd' => $command,
            'io' => $io,
            'input' => $input
        ];
        return $this;
    }

    /**
     * Runs a command with specific input.
     *
     * @param  Command $command
     * @param  IO $command
     * @param  string|null  $input
     * @return self
     */
    public function run (Command $command, IO $io, $input = null)
    {
        $command->run($io, $input);
        return $this;
    }

    /**
     * Starts the execution of commands.
     *
     * @return self
     */
    public function start ()
    {
        while (count($this->commands) > 0) {
            $toRun = array_shift($this->commands);
            $this->run($toRun['cmd'], $toRun['io'], $toRun['input']);
        }
        return $this;
    }

    /**
     * Clears the list of commands.
     *
     * @return self
     */
    public function clear ()
    {
        $this->commands = [];
        return $this;
    }
}
