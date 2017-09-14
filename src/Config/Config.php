<?php namespace Tarsana\Command\Config;

use Tarsana\Command\Interfaces\Config\ConfigInterface;

/**
 * Stores and gets configuration.
 */
class Config implements ConfigInterface {
    /**
     * The raw configuration data.
     *
     * @var array
     */
    protected $data;

    public function __construct(array $data) {
        $this->data = $data;
    }

    /**
     * Gets a configuration value by path.
     */
    public function get(string $path = null)
    {
        if (null === $path)
            return $this->data;
        $keys = explode('.', $path);
        $value = $this->data;
        foreach ($keys as $key) {
            if (!is_array($value) || !array_key_exists($key, $value))
                throw new \Exception("Unable to find a configuration value with path '{$path}'");
            $value = $value[$key];
        }
        return $value;
    }
}
