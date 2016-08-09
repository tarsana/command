<?php namespace Tarsana\Command;

use Tarsana\Command\Interfaces\DispatcherInterface;

/**
 * Commands Dispatcher.
 * Give possibility to register commands with
 * names and call them from within other commands.
 */
class Dispatcher implements DispatcherInterface {

    /**
     * Registers a command with name. The $command
     * argument can be a Command class or object.
     *
     * @param  string $name
     * @param  string|Tarsana\Command\Command $command
     * @return void
     */
    public function register($name, $command)
    {

    }

    /**
     * Runs a command with the provided arguments and console.
     *
     * @param  string $name
     * @param  string|stdClass $args
     * @param  League\CLImate\CLImate $console
     * @return void
     */
    public function call($name, $args = null, CLImate $console = null)
    {

    }

}
