<?php namespace Tarsana\Command;

use Tarsana\Command\Commands\HelpCommand;
use Tarsana\Command\Commands\VersionCommand;
use Tarsana\Command\Console\Console;
use Tarsana\Command\Console\ExceptionPrinter;
use Tarsana\Command\Interfaces\Console\ConsoleInterface;
use Tarsana\Command\Interfaces\Template\TemplateLoaderInterface;
use Tarsana\Command\SubCommand;
use Tarsana\Command\Template\TemplateLoader;
use Tarsana\IO\Filesystem;
use Tarsana\IO\FilesystemInterface;
use Tarsana\Syntax\Exceptions\ParseException;
use Tarsana\Syntax\Factory as S;
use Tarsana\Syntax\Text as T;

class Command {

    protected $name;
    protected $version;
    protected $description;

    protected $syntax;
    protected $descriptions;

    protected $options;
    protected $args;

    protected $action;
    protected $commands;

    protected $console;
    protected $fs;
    protected $templatesLoader;

    public static function create(callable $action = null) {
        $command = new Command;
        if (null !== $action)
            $command->action($action);
        return $command;
    }

    public function __construct()
    {
        $this->commands = [];
        $this->setupSubCommands()
             ->name('Unknown')
             ->version('1.0.0')
             ->description('...')
             ->descriptions([])
             ->options([])
             ->console(new Console)
             ->fs(new Filesystem('.'))
             ->init();
    }

    /**
     * name getter and setter.
     *
     * @param  string
     * @return mixed
     */
    public function name(string $value = null)
    {
        if (null === $value) {
            return $this->name;
        }
        $this->name = $value;
        return $this;
    }

    /**
     * version getter and setter.
     *
     * @param  string
     * @return mixed
     */
    public function version(string $value = null)
    {
        if (null === $value) {
            return $this->version;
        }
        $this->version = $value;
        return $this;
    }

    /**
     * description getter and setter.
     *
     * @param  string
     * @return mixed
     */
    public function description(string $value = null)
    {
        if (null === $value) {
            return $this->description;
        }
        $this->description = $value;
        return $this;
    }

    /**
     * descriptions getter and setter.
     *
     * @param  string
     * @return mixed
     */
    public function descriptions(array $value = null)
    {
        if (null === $value) {
            return $this->descriptions;
        }
        $this->descriptions = $value;
        return $this;
    }

    /**
     * syntax getter and setter.
     *
     * @param  string|null $syntax
     * @return Syntax|self
     */
    public function syntax(string $syntax = null)
    {
        if (null === $syntax)
            return $this->syntax;

        $this->syntax = S::syntax()->parse("{{$syntax}| }");
        return $this;
    }

    /**
     * options getter and setter.
     *
     * @param  array
     * @return mixed
     */
    public function options(array $options = null)
    {
        if (null === $options) {
            return $this->options;
        }

        $this->options = [];
        foreach($options as $option)
            $this->options[$option] = false;

        return $this;
    }

    /**
     * option getter.
     *
     * @param  string
     * @return mixed
     */
    public function option(string $name)
    {
        if (!array_key_exists($name, $this->options))
            throw new \InvalidArgumentException("Unknown option '{$name}'");
        return $this->options[$name];
    }

    /**
     * args getter and setter.
     *
     * @param  stdClass
     * @return mixed
     */
    public function args(\stdClass $value = null)
    {
        if (null === $value) {
            return $this->args;
        }
        $this->args = $value;
        return $this;
    }

    /**
     * console getter and setter.
     *
     * @param  ConsoleInterface
     * @return mixed
     */
    public function console(ConsoleInterface $value = null)
    {
        if (null === $value) {
            return $this->console;
        }
        $this->console = $value;
        foreach ($this->commands as $name => $command) {
            $command->console = $value;
        }
        return $this;
    }

    /**
     * fs getter and setter.
     *
     * @param  Tarsana\IO\Filesystem|string
     * @return mixed
     */
    public function fs($value = null)
    {
        if (null === $value) {
            return $this->fs;
        }
        if (is_string($value))
            $value = new Filesystem($value);
        $this->fs = $value;
        foreach ($this->commands as $name => $command) {
            $command->fs = $value;
        }
        return $this;
    }

    /**
     * templatesLoader getter and setter.
     *
     * @param  Tarsana\Command\Interfaces\Template\TemplateLoaderInterface
     * @return mixed
     */
    public function templatesLoader(TemplateLoaderInterface $value = null)
    {
        if (null === $value) {
            return $this->templatesLoader;
        }
        $this->templatesLoader = $value;
        return $this;
    }

    public function templatesPath(string $path, string $cachePath = null) {
        $this->templatesLoader = new TemplateLoader($path, $cachePath);
        return $this;
    }

    public function template(string $name) {
        if (null === $this->templatesLoader)
            throw new \Exception("Please initialize the templates loader before trying to load templates!");
        return $this->templatesLoader->load($name);
    }

    /**
     * action getter and setter.
     *
     * @param  callable
     * @return mixed
     */
    public function action(callable $value = null)
    {
        if (null === $value) {
            return $this->action;
        }
        $this->action = $value;
        return $this;
    }

    public function command(string $name, Command $command = null)
    {
        if (null === $command) {
            if (!array_key_exists($name, $this->commands))
                throw new \InvalidArgumentException("subcommand '{$name}' not found!");
            return $this->commands[$name];
        }
        $this->commands[$name] = $command;
        return $this;
    }

    protected function setupSubCommands()
    {
        return $this->command('--help', new HelpCommand($this))
             ->command('--version', new VersionCommand($this));
    }

    public function describe(string $name, string $description = null)
    {
        if (null === $description)
            return array_key_exists($name, $this->descriptions)
                ? $this->descriptions[$name] : '';
        if (substr($name, 0, 2) == '--' && array_key_exists($name, $this->options())) {
            $this->descriptions[$name] = $description;
            return $this;
        }
        try {
            $this->syntax->field($name);
            // throws exception if field is missing
            $this->descriptions[$name] = $description;
            return $this;
        } catch (\Exception $e) {
            throw new \InvalidArgumentException("Unknown field '{$name}'");
        }
    }

    public function run(array $args = null, array $options = [], bool $rawArgs = true)
    {
        try {
            $this->clear();

            if ($rawArgs) {
                if (null === $args) {
                    $args = $GLOBALS['argv'];
                    array_shift($args);
                }

                if (!empty($args) && array_key_exists($args[0], $this->commands)) {
                    $name = $args[0];
                    array_shift($args);
                    return $this->command($name)->run($args);
                }

                $this->parseArguments($args);
            } else {
                $this->args = (object) $args;
                foreach ($options as $name) {
                    if (!array_key_exists($name, $this->options))
                        throw new \Exception("Unknown option '{$name}'");
                    $this->options[$name] = true;
                }
            }

            if (null === $this->action)
                $this->execute();
            else
                ($this->action)($this);
        } catch (\Exception $e) {
            $this->handleError($e);
        }
    }

    public function clear()
    {
        $this->args = null;
        foreach($this->options as $name => $value) {
            $this->options[$name] = false;
        }
    }

    public function parseArguments(array $args)
    {
        if (null === $this->syntax) {
            $this->args = null;
            return;
        }

        $arguments = [];
        foreach ($args as &$arg) {
            if (array_key_exists($arg, $this->options))
                $this->options[$arg] = true;
            else
                $arguments[] = $arg;
        }
        $arguments = T::join($arguments, ' ');
        $this->args = $this->syntax->parse($arguments);
    }

    protected function handleError(\Exception $e) {
        $output = (new ExceptionPrinter)->print($e);
        $this->console()->error($output);
    }

    protected function init() {}
    protected function execute() {}

}
