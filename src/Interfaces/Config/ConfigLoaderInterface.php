<?php namespace Tarsana\Command\Interfaces\Config;

use Tarsana\Command\Interfaces\Config\ConfigInterface;

/**
 * Loads configuration from multiple files.
 */
interface ConfigLoaderInterface {

    public function load(array $paths) : ConfigInterface;

}
