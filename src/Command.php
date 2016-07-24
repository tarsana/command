<?php namespace Tarsana\Application;

use League\CLImate\CLImate;
use Tarsana\Application\Exceptions\CommandException;
use Tarsana\Application\TemplateLoader;
use Tarsana\Functional as F;
use Tarsana\IO\Filesystem;
use Tarsana\Syntax\Factory as S;
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
     * The command description.
     *
     * @var string
     */
    protected $description;

    /**
     * Syntax to parse the command input.
     *
     * @var Tarsana\Syntax\Syntax
     */
    protected $syntax;

    /**
     * template loader.
     *
     * @var Tarsana\Application\TemplateLoader
     */
    protected $templateLoader;

    /**
     * Tells if the command is internal (not shown in help message).
     *
     * @var bool
     */
    protected $isInternal;

    /**
     * Command line reader/writer.
     *
     * @var League\CLImate\CLImate
     */
    protected $cli;

    /**
     * Command line arguments.
     *
     * @var mixed
     */
    protected $args;

    /**
     * Creates a new Command.
     *
     * @param Application|null $app
     */
    public function __construct (Application $app = null)
    {
        $this->app = $app;
        $this->templateLoader = null;
        $this->isInternal = false;

        if(null !== $this->syntax)
            $this->syntax($this->syntax);
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
     * isInternal getter and setter.
     *
     * @param  bool|void $value
     * @return bool|self
     */
    public function isInternal ($value = null)
    {
        if (null === $value)
            return $this->isInternal;
        $this->isInternal = $value;
        return $this;
    }

    /**
     * cli getter and setter.
     *
     * @param  League\CLImate\CLImate|void $value
     * @return League\CLImate\CLImate|self
     */
    public function cli ($value = null)
    {
        if (null === $value) {
            if (null === $this->cli) {
                throw new NullPropertyAccess("Trying to access null property 'cli' of a command");
            }
            return $this->cli;
        }
        $this->cli = $value;
        return $this;
    }

    /**
     * Template loader getter and setter.
     *
     * @param  Tarsana\Application\TemplateLoader|void $value
     * @return Tarsana\Application\TemplateLoader|self
     */
    public function templateLoader (TemplateLoader $value = null)
    {
        if (null === $value)
            return $this->templateLoader;
        $this->templateLoader = $value;
        return $this;
    }

    /**
     * Templates path getter and setter.
     *
     * @param  string|void $value
     * @return string|self
     */
    public function templatesPath ($value = null)
    {
        if (null === $value)
            return $this->templatesLoader->fs()->path();
        $this->templatesLoader = new TemplateLoader(new Filesystem($value));
        return $this;
    }

    /**
     * Syntax getter and setter.
     *
     * @param  Tarsana\Syntax\Syntax|string|null $value
     * @return Tarsana\Syntax\Syntax|self
     */
    public function syntax ($value = null)
    {
        if (null === $value)
            return $this->syntax;
        if ($value instanceof Syntax)
            $this->syntax = $value;
        else if (is_string($value)) {
            $syntax = F\s($value)
                ->then(F\chunks([['(',')'],['{','}'],['[',']'],['"','"']], ' '))
                ->then(F\pipe(F\join(','), F\prepend('{ ,'), F\append('}')))
                ->get();
            $ss = S::syntax();
            if ($ss->canParse($syntax))
                $this->syntax = $ss->parse($syntax);
            else
                throw new \InvalidArgumentException("Invalid Syntax: '{$value}'");
        } else {
            throw new \InvalidArgumentException("Trying to set invalid syntax to command");
        }
        return $this;
    }

    /**
     * Runs the command.
     *
     * @param  League\CLImate\CLImate $cli
     * @param  string|null $args
     * @return void
     */
    public function run (CLImate $cli, $args = '')
    {
        $this->args = $args;
        if (is_string($this->syntax))
            $this->syntax($this->syntax);
        if ($this->syntax !== null)
            $this->args = $this->syntax->parse($args);
        $this->cli($cli)->handle();
    }

    protected function template ($name)
    {
        if ($this->templatesLoader)
            return $this->templatesLoader->load($name);

        return $this->app()->templateLoader()->load($name);
    }

    public function init () {}

    abstract public function handle ();
}
