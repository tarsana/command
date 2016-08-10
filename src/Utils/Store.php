<?php namespace Tarsana\Command\Utils;

use Tarsana\Command\Exceptions\StoreException;

/**
 * Store is a container of values of the same type, indexed by strings.
 * It gives the possibility to store, remove, get values by name.
 */
class Store {

    /**
     * The class of elements to be stored.
     *
     * @var string
     */
    protected $type;

    /**
     * Array of the stored elements.
     *
     * @var array
     */
    protected $elements;

    /**
     * Creates a new Store.
     *
     * @param string $type
     * @throws StoreException
     */
    public function __construct($type)
    {
        if (!is_string($type) || !class_exists($type)) {
            $this->error('The class given to the store constructor is invalid');
        }
        $this->type = $type;
        $this->elements = [];
    }

    /**
     * type getter.
     *
     * @return string
     */
    public function type()
    {
        return $this->type;
    }

    /**
     * elements getter.
     *
     * @return array
     */
    public function elements()
    {
        return $this->elements;
    }

    /**
     * Stores a new element, `value` sould be an instance or a subclass
     * of `type`. If `value` is a class, an instance of that class will
     * be created when trying the get the element,and the same instance
     * will be returned for next `get()` calls.
     *
     * @param  string $name
     * @param  mixed $value
     * @return self
     * @throws StoreException
     */
    public function store($name, $value)
    {
        // Validates the name
        if (! is_string($name)) {
            $this->error('The name must be a string');
        }
        if ($this->contains($name)) {
            $this->error("The name '{$name}' already exists in the store");
        }

        // Validates the value
        if (is_string($value)) {
            if (! class_exists($value)) {
                $this->error("Trying to store an unknow class '{$value}'");
            }
            if ($value != $this->type && !is_subclass_of($value, $this->type)) {
                $this->error("Trying to store an instance of '{$value}' in a store of '{$this->type}'");
            }
        } else if (is_object($value)) {
            $type = get_class($value);
            if ($type != $this->type && !is_subclass_of($type, $this->type)) {
                $this->error("Trying to store an instance of '{$type}' in a store of '{$this->type}'");
            }
        } else {
            $this->error('Trying to store a value which is not a class or object');
        }

        // Storing the value
        $this->elements[$name] = $value;
        return $this;
    }

    /**
     * Checks if the store contains an element with the provided name.
     *
     * @param  string $name
     * @return bool
     */
    public function contains($name)
    {
        return isset($this->elements[$name]);
    }

    /**
     * Gets an element by name.
     *
     * @param  string $name
     * @return Tarsana\Command\Command
     * @throws StoreException
     */
    public function get($name)
    {
        if (! $this->contains($name)) {
            $this->error("The element with name '{$name}' is missing");
        }

        $value = $this->elements[$name];
        if (is_string($value) && class_exists($value)) {
            $this->elements[$name] = new $value;
        }

        return $this->elements[$name];
    }

    /**
     * Removes an element from the store.
     *
     * @param  string $name
     * @return self
     */
    public function remove($name)
    {
        if ($this->contains($name)) {
            unset($this->elements[$name]);
        }
        return $this;
    }

    /**
     * Throws a StoreException with a specific message.
     *
     * @param  string $message
     * @return void
     * @throws StoreException
     */
    protected function error($message)
    {
        throw new StoreException($message);
    }

}
