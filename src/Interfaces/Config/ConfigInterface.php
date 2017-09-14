<?php namespace Tarsana\Command\Interfaces\Config;

/**
 * Stores and gets configuration.
 */
interface ConfigInterface {
    /**
     * Gets a configuration value by path.
     */
    public function get(string $path);
}
