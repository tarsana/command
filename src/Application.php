<?php namespace Tarsana\Application;

use Tarsana\Application\Commands\Command;
use Tarsana\Application\Exceptions\CommandNotFound;
use Tarsana\Application\Runner;
use Tarsana\Application\Syntaxes\ApplicationInputSyntax;
use Tarsana\Functional as F;
use Tarsana\Functional\Stream;
use Tarsana\IO\Resources\InputResource;
use Tarsana\Syntax\Syntax;

class Application {

    /**
     * The name of the application.
     *
     * @var string
     */
    protected $name;

    /**
     * The description of the application.
     *
     * @var string
     */
    protected $description;

    /**
     * The version of the application.
     *
     * @var string
     */
    protected $version;

    /**
     * The list of commands.
     *
     * @var Tarsana\Application\Commands\Command[]
     */
    protected $commands;

    /**
     * Context data shared between commands.
     *
     * @var array
     */
    protected $context;

    /**
     * Object running the commands.
     *
     * @var Tarsana\Application\Runner
     */
    protected $runner;

    /**
     * Creates an application.
     *
     * @param string $name
     * @param string $version
     * @param string $description
     */
    public function __construct ($name = 'Unknown', $version = '1.0.0', $description = '')
    {
        $this->name = $name;
        $this->version = $version;
        $this->description = $description;
        $this->commands = [];
        $this->context = [];
        $this->runner = new Runner;
        $this->addDefaultCommands();
    }

    /**
     * Name getter and setter.
     *
     * @param  string|void $value
     * @return string|self
     */
    public function name ($value = null)
    {
        if (null === $value)
            return $this->name;
        $this->name = $value;
        return $this;
    }

    /**
     * Description getter and setter.
     *
     * @param  string|void $value
     * @return string|self
     */
    public function description ($value = null)
    {
        if (null === $value)
            return $this->description;
        $this->description = $value;
        return $this;
    }

    /**
     * Version getter and setter.
     *
     * @param  string|void $value
     * @return string|self
     */
    public function version ($value = null)
    {
        if (null === $value)
            return $this->version;
        $this->version = $value;
        return $this;
    }

    /**
     * Runner getter and setter.
     *
     * @param  Tarsana\Application\Runner|void $value
     * @return Tarsana\Application\Runner|self
     */
    public function runner (Runner $value = null)
    {
        if (null === $value)
            return $this->runner;
        $this->runner = $value;
        return $this;
    }

    /**
     * Checks if the application has a command with the provided name.
     *
     * @param  string  $name
     * @return boolean
     */
    public function hasCommand ($name)
    {
        return isset($this->commands[$name]);
    }

    /**
     * Single command getter and setter.
     *
     * @param  string $name
     * @param  Tarsana\Application\Commands\Command|void $value
     * @return Tarsana\Application\Commands\Command|self
     * @throws Tarsana\Application\Exceptions\CommandNotFound
     */
    public function command ($name, Command $value = null)
    {
        if (null === $value) {
            if ($this->hasCommand($name))
                return $this->commands[$name];
            throw new CommandNotFound("Unable to find command '{$name}' in the application '{$this->name}'");
        }

        $value->app($this);
        $value->init();
        $this->commands[$name] = $value;
        return $this;
    }

    /**
     * Commands getter.
     *
     * @return array
     */
    public function commands ()
    {
        return $this->commands;
    }

    /**
     * Runs the application.
     *
     * @param  string  $args
     * @param  IO|null $io
     * @return void
     */
    public function run ($args = null, IO $io = null, Syntax $syntax = null)
    {
        if (null === $args)
            $args = Stream::of($_SERVER['argv'])
                ->then('Tarsana\Functional\tail')
                ->then(F\join(' '))
                ->get();
        if (null === $io)
            $io = new IO();
        if(null === $syntax)
            $syntax = new ApplicationInputSyntax;

        $input = $syntax->parse($args);
        $done = false;
        foreach ($input->flags() as $flag) {
            if ($this->hasCommand($flag)) {
                $this->scheduleCommand($flag, $io);
                $done = true;
                break;
            }
        }
        if (! $done) {
            if ($input->command()) {
                $this->scheduleCommand($input->command(), $io, $input->args());
            } else {
                $this->scheduleCommand('', $io); // default command
            }
        }
        $this->runner->start();
    }

    /**
     * Schedule a command to be executed.
     *
     * @param  string $name
     * @param  string|null $input
     * @return void
     */
    public function scheduleCommand ($name, IO $io, $input = null)
    {
        if ($this->hasCommand($name)) {
            $this->runner->schedule($this->command($name), $io, $input);
        } else {
            $this->runner->schedule($this->command('error'), $io, "cmd-not-found {$name}");
        }
    }

    /**
     * Adds default commands to the application.
     *
     * @return void
     */
    protected function addDefaultCommands ()
    {
        $helpCommand = new Commands\HelpCommand;
        $errorCommand = new Commands\ErrorCommand;
        $versionCommand = new Commands\VersionCommand;

        $this->command('', $helpCommand);
        $this->command('help', $helpCommand);
        $this->command('--help', $helpCommand);
        $this->command('error', $errorCommand);
        $this->command('version', $versionCommand);
        $this->command('--version', $versionCommand);
    }

}
