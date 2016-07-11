<?php namespace Tarsana\Command;

use Tarsana\IO\Filesystem;
use League\CLImate\CLImate;
use Tarsana\Functional as F;
use Tarsana\Functional\Stream;
use Tarsana\Command\Environment;
use Tarsana\Command\TemplateLoader;
use Tarsana\Command\Commands\HelpCommand;
use Tarsana\Command\Syntax\SyntaxBuilder;
use Tarsana\Command\Commands\VersionCommand;
use Tarsana\Command\Exceptions\CommandNotFound;
use Tarsana\Command\Exceptions\NullPropertyAccess;
use Tarsana\Command\Interfaces\TemplateLoaderInterface;

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
     * Syntax builder; it provides a syntax which
     * is used to parse the command input.
     *
     * @var Tarsana\Command\Syntax\SyntaxBuilder
     */
    protected $syntaxBuilder;

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
     * Filesystem handler.
     *
     * @var Tarsana\IO\Filesystem
     */
    protected $fs;

    /**
     * Templates loader.
     *
     * @var Tarsana\Command\Interfaces\TemplateLoaderInterface
     */
    protected $templateLoader;

    /**
     * Creates a new Command.
     */
    public function __construct()
    {
        $this->templateLoader = new TemplateLoader;
        $this->fs = new Filesystem('.');
        $this->syntaxBuilder = SyntaxBuilder::of('');
        $this->description = '...';
        $this->version = '0.0.1';
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
    public function name($value = null)
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
    public function description($value = null)
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
    public function version($value = null)
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
    public function console($value = null)
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
    public function syntax($value = null)
    {
        if (null === $value)
            return $this->syntaxBuilder->get();

        $this->syntaxBuilder = SyntaxBuilder::of($value);

        return $this;
    }

    /**
     * Describes a field or subfield of the syntax.
     * `$name` has the format "field.subfield...".
     *
     * @param  string $name
     * @param  string $description
     * @param  mixed  $default
     * @return self
     */
    public function describe($name, $description, $default = null)
    {
        $this->syntaxBuilder->describe($name, $description, $default);
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
     * @throws Tarsana\Command\Exceptions\CommandNotFound
     */
    public function command($name, Command $cmd = null)
    {
        if (null === $cmd) {
            if (!$this->hasCommand($name))
                throw new CommandNotFound("Command '{$this->name}' has no subcommand '{$name}'");
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
    public function hasCommand($name) {
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
     * Filesystem getter and setter.
     *
     * @param  Tarsana\IO\Filesystem|null
     * @return Tarsana\IO\Filesystem|self
     */
    public function fs(Filesystem $value = null)
    {
        if (null === $value) {
            return $this->fs;
        }
        $this->fs = $value;
        return $this;
    }

    /**
     * Templates Loader getter and setter.
     *
     * @param  Tarsana\Command\Interfaces\TemplateLoaderInterface|null
     * @return Tarsana\Command\Interfaces\TemplateLoaderInterface|self
     */
    public function templateLoader(TemplateLoaderInterface $value = null)
    {
        if (null === $value) {
            return $this->templatesLoader;
        }
        $this->templatesLoader = $value;
        return $this;
    }

    /**
     * Sets the template paths.
     *
     * @param  string $templatesPath
     * @param  string $cachePath
     * @return self
     */
    public function templatePaths($templatesPath, $cachePath = null)
    {
        $this->templateLoader->init($templatesPath, $cachePath);
        return $this;
    }

    /**
     * Runs the command.
     *
     * @param  string|null $args
     * @param  League\CLImate\CLImate|null $console
     * @return void
     */
    public function run($args = null, CLImate $console = null)
    {
        try {

            if (null === $args)
                $args = $this->consoleArguments();
            if (null === $console)
                $console = new CLImate;

            $firstArg = F\head(F\split(' ', $args));
            if ($this->hasCommand($firstArg)) {
                $args = F\join(' ', F\tail(F\split(' ', $args)));
                $this->command($firstArg)->run($args, $console);
            } else {
                $this->console($console);
                if (! $this->syntax()->canParse($args)) {
                    $errors = F\s($this->syntax()->checkParse($args))
                        ->then(F\append("Invalid arguments: '{$args}' for command '{$this->name}'"))
                        ->then(F\join(PHP_EOL))
                        ->get();
                    $this->error($errors);
                }

                else
                    $this->args($this->syntax()->parse($args))
                         ->execute();
            }
        } catch (\Exception $e) {
            $this->error(
                $e->getMessage()
                . PHP_EOL . PHP_EOL .
                $e->getTraceAsString()
            );
        }
    }

    /**
     * Loads a template by name.
     *
     * @param  string $name
     * @return Tarsana\Command\Interfaces\TemplateInterface
     */
    protected function template($name)
    {
        if (null === $this->templateLoader)
            throw new NullPropertyAccess("The templatesLoader is not initialized; Please set it before trying to load a template !");

        return $this->templateLoader->load($name);
    }

    /**
     * Returns command line arguments as string.
     *
     * @return string
     */
    protected function consoleArguments()
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

    /**
     * Adds Help and Version sub commands.
     */
    protected function addDefaultSubCommands()
    {
        if (! ($this instanceof HelpCommand) && ! ($this instanceof VersionCommand)) {
            $this->command('--version', new VersionCommand($this));
            $this->command('--help', new HelpCommand($this));
        }
    }

    /**
     * Shows an error message and stops the execution of the command.
     *
     * @param  string $message
     * @return void
     */
    protected function error($message)
    {
        $this->console->style->addCommand('error', 'white');
        $this->console->backgroundRed()->error($message);
        exit(1);
    }

    /**
     * Calls a command on the environment store.
     *
     * @param  string  $name
     * @param  string  $args
     * @param  CLImate $console
     * @return void
     */
    protected function call($name, $args = null, CLImate $console = null)
    {
        return Environment::get()
            ->command($name)
            ->run($args, $console);
    }

    /**
     * Initializes the command. Override it to set the command
     * description, version, syntax, filesystem, templates
     * laoder and any other configuration.
     *
     * @return void
     */
    protected function init() {}

    /**
     * The logic of the command goes in this method.
     *
     * @return self
     */
    abstract protected function execute();
}
