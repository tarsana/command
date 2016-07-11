<?php namespace Tarsana\Application\Commands;

use Tarsana\Application\Application;
use Tarsana\Application\IO;
use Tarsana\Syntax\StringSyntax;
use Tarsana\Syntax\Syntax;

/**
 * An abstract command class; the parent of all commands.
 */
abstract class Command {

    /**
     * The application containing this command.
     *
     * @var Tarsana\Application\Application
     */
    protected $app;

    /**
     * Syntax to parse the command input.
     *
     * @var Tarsana\Syntax\Syntax
     */
    protected $syntax;

    /**
     * Creates a new Command.
     *
     * @param Application|null $app
     */
    public function __construct (Application $app = null)
    {
        $this->app = $app;
    }

    /**
     * Application getter and setter.
     *
     * @param  Tarsana\Application\Application|void $value
     * @return Tarsana\Application\Application|self
     */
    public function app (Application $value = null)
    {
        if (null === $value)
            return $this->app;
        $this->app = $value;
        return $this;
    }

    /**
     * Syntax getter and setter.
     *
     * @param  Tarsana\Syntax\Syntax|void $value
     * @return Tarsana\Syntax\Syntax|self
     */
    public function syntax (Syntax $value = null)
    {
        if (null === $value)
            return $this->syntax;
        $this->syntax = $value;
        return $this;
    }

    /**
     * Runs the command.
     *
     * @param  IO          $io
     * @param  string|null $args
     * @return void
     */
    public function run (IO $io, $args = null)
    {
        $input = null;
        if ($args !== null)
            if ($this->syntax !== null)
                $input = $this->syntax->parse($args);
            else
                $input = $args;
        $this->handle($io, $input);
    }

    public function init () {}

    abstract public function handle (IO $io, $input = null);
}
