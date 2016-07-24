<?php namespace Tarsana\Application;

use League\CLImate\CLImate;
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
     * @param  League\CLImate\CLImate $cli
     * @param  string|null  $args
     * @return self
     */
    public function schedule (Command $command, CLImate $cli, $args = null)
    {
        $this->commands[] = [
            'cmd' => $command,
            'cli' => $cli,
            'args' => $args
        ];
        return $this;
    }

    /**
     * Runs a command.
     *
     * @param  Command $command
     * @param  League\CLImate\CLImate $cli
     * @param  string|null  $args
     * @return self
     */
    public function run (Command $command, CLImate $cli, $args = null)
    {
        $command->run($cli, $args);
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
            $this->run($toRun['cmd'], $toRun['cli'], $toRun['args']);
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
