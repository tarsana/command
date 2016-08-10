<?php namespace Tarsana\Command;

use Tarsana\Command\Utils\Store;

/**
 * This is a Singleton class holding general/shared
 * information between all commands.
 */
class Environment {

    /**
     * Instance of Environment.
     *
     * @var Tarsana\Command\Environment
     */
    protected static $instance;

    /**
     * Gets the singleton instance.
     *
     * @return Tarsana\Command\Environment
     */
    public static function get()
    {
        if (null === self::$instance) {
            self::$instance = new Environment;
        }
        return self::$instance;
    }

    /**
     * The store of commands.
     *
     * @var Tarsana\Command\Utils\Store
     */
    protected $store;

    /**
     * private constructor
     */
    private function __construct()
    {
        $this->store = new Store('Tarsana\Command\Command');
    }

    /**
     * store getter.
     *
     * @return Tarsana\Command\Utils\Store
     */
    public function store()
    {
        return $this->store;
    }

    /**
     * Checks if the store contains a command with the given name.
     *
     * @param  string  $name
     * @return boolean
     */
    public function hasCommand($name)
    {
        return $this->store->contains($name);
    }

    /**
     * Adds/Gets a command to/from the store.
     * @param  string $name
     * @param  string|Tarsana\Command\Command $value
     * @return Tarsana\Command\Command|self
     */
    public function command($name, $value = null)
    {
        if (null === $value) {
            if (! $this->hasCommand($name)) {
                throw new EnvironmentException("Unable to find the command '{$name}'");
            }
            return $this->store->get($name);
        }

        $this->store->store($name, $value);
        return $this;
    }

}
