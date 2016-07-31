<?php namespace Tarsana\Command;

use League\CLImate\CLImate;
use Tarsana\Command\Commands\HelpCommand;
use Tarsana\Command\Commands\VersionCommand;
use Tarsana\Functional as F;
use Tarsana\Functional\Stream;
use Tarsana\Syntax\Factory as S;

/**
 * An abstract command class; the parent of all commands.
 */
abstract class Command {

    /**
     * The command name.
     *
     * @var string
     */
    protected $name;

    /**
     * The command description.
     *
     * @var string
     */
    protected $description;

    /**
     * The command version.
     *
     * @var string
     */
    protected $version;

    /**
     * Syntax used to parse the command input.
     *
     * @var Tarsana\Syntax\Syntax
     */
    protected $syntax;

    /**
     * List of sub commands
     *
     * @var Tarsana\Command\Command[]
     */
    protected $subCommands;

    /**
     * Command line reader/writer.
     *
     * @var League\CLImate\CLImate
     */
    protected $console;

    /**
     * The result of parsing the input using syntaxes.
     *
     * @var mixed
     */
    protected $args;

    /**
     * Creates a new Command.
     *
     */
    public function __construct ()
    {
        $this->syntax = S::string('');
        $this->version = '0.0.1';
        $this->description = '...';
        $this->name = 'unknown';
        $this->console = null;
        $this->args = null;

        $this->addDefaultSubCommands();
        $this->init();
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
     * version getter and setter.
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
     * Console getter and setter.
     *
     * @param  League\CLImate\CLImate|void $value
     * @return League\CLImate\CLImate|self
     */
    public function console ($value = null)
    {
        if (null === $value)
            return $this->console;

        $this->console = $value;
        return $this;
    }

    /**
     * Syntax getter and setter.
     *
     * @param  Tarsana\Syntax\Syntax|string|null $value
     * @return Tarsana\Syntax\Syntax|self
     * @throws InvalidArgumentException
     */
    public function syntax ($value = null)
    {
        if (null === $value)
            return $this->syntax;

        if ($value instanceof Syntax)
            $this->syntax = $value;

        else if (is_string($value))
            $this->syntax = $this->syntaxFromString($value);

        else
            throw new \InvalidArgumentException("Trying to set invalid syntax to command");

        return $this;
    }

    /**
     * subCommands getter and setter.
     *
     * @param  array|null
     * @return array|self
     */
    public function subCommands($value = null)
    {
        if (null === $value) {
            return $this->subCommands;
        }
        $this->subCommands = $value;
        return $this;
    }

    /**
     * adds/overrides or gets a sub command.
     *
     * @param  string  $name
     * @param  Tarsana\Command\Command|null $cmd
     * @return Tarsana\Command\Command
     */
    public function command ($name, Command $cmd = null)
    {
        if (null === $cmd) {
            if (!$this->hasCommand($name))
                throw new CommandNotFound("Command '{$name}' not found");
            return $this->subCommands[$name];
        }

        $this->subCommands[$name] = $cmd;
        return $this;
    }

    /**
     * Checks if a sub command with the provided name exists.
     *
     * @param  string  $name
     * @return boolean
     */
    public function hasCommand ($name) {
        return isset($this->subCommands[$name]);
    }

    /**
     * args getter and setter.
     *
     * @param  stdClass|null
     * @return stdClass|self
     */
    public function args($value = null)
    {
        if (null === $value)
            return $this->args;

        $this->args = $value;
        return $this;
    }

    /**
     * make a Syntax from custom string.
     *
     * @param  string $value
     * @return Tarsana\Syntax\Syntax
     * @throws InvalidArgumentException
     */
    protected function syntaxFromString ($value)
    {
        $syntax = F\s($value)->then(        // "first name" [#age]
            F\chunks('(){}[]""', ' '),      // ["first name", [#age]]
            F\join(','),                    // "first name",[#age]
            F\prepend('{ ,'), F\append('}') // { ,"first name",[#age]}
        )->get();

        $ss = S::syntax();

        if (!$ss->canParse($syntax))
            throw new \InvalidArgumentException("Invalid Syntax: '{$syntax}'");

        return $ss->parse($syntax);
    }

    /**
     * Runs the command.
     *
     * @param  string|null $args
     * @param  League\CLImate\CLImate|null $console
     * @return void
     */
    public function run ($args = null, CLImate $console = null)
    {
        if (null === $args)
            $args = $this->consoleArguments();
        if (null === $console)
            $console = new CLImate;

        $firstArg = F\head(F\split(' ', $args));
        if ($this->hasCommand($firstArg)) {
            $this->command($firstArg)->run(
                F\join(' ', F\tail(F\split(' ', $args))),
                $console
            );
        } else {
            $this->args($this->syntax->parse($args))
                 ->console($console)
                 ->execute();
        }
    }

    /**
     * Returns command line arguments as string.
     *
     * @return string
     */
    protected function consoleArguments ()
    {
        return Stream::of($_SERVER['argv'])    // ['script.php', foo', 'lorem ipsum']
            ->then(F\f('tail'))                // ['foo', 'lorem ipsum']
            ->map(function($arg) {
                return (F\contains(' ', $arg))
                    ? "\"{$arg}\""
                    : $arg;
            })                                 // ['foo', '"lorem ipsum"']
            ->then(F\join(' '))                // 'foo "lorem ipsum"'
            ->get();
    }

    protected function addDefaultSubCommands ()
    {
        if (! ($this instanceof HelpCommand) && ! ($this instanceof VersionCommand)) {
            $helpCommand = new HelpCommand($this);
            $this->command('help', $helpCommand)
                 ->command('--help', $helpCommand);

            $versionCommand = new VersionCommand($this);
            $this->command('version', $versionCommand)
                 ->command('some-very-long-command-name', $versionCommand)
                 ->command('--version', $versionCommand);
        }
    }

    /**
     * Initializes the command.
     * Used to set the command description,
     * syntax and any other configuration.
     *
     * @return void
     */
    protected function init () {}

    /**
     * The logic of the command goes in this method.
     *
     * @return self
     */
    abstract protected function execute ();
}
