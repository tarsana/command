<?php namespace Tarsana\Command\Config;

use Tarsana\Command\Config\Config;
use Tarsana\Command\Helpers\Decoders\JsonDecoder;
use Tarsana\Command\Interfaces\Config\ConfigInterface;
use Tarsana\Command\Interfaces\Config\ConfigLoaderInterface;
use Tarsana\IO\Interfaces\Filesystem as FilesystemInterface;

/**
 * Loads configuration from multiple files.
 */
class ConfigLoader implements ConfigLoaderInterface {

    protected static $decoders = [
        'json' => JsonDecoder::class
    ];

    protected $fs;

    public function __construct(FilesystemInterface $fs)
    {
        $this->fs = $fs;
    }

    public function load(array $paths) : ConfigInterface
    {
        if (empty($paths))
            return new Config([]);
        $data = [];
        foreach ($paths as $path) {
            $data[] = $this->decode($path);
        }
        $data = call_user_func_array('array_replace_recursive', $data);
        return new Config($data);
    }

    protected function decode(string $path) : array {
        if (! $this->fs->isFile($path))
            return [];
        $file = $this->fs->file($path);
        $ext  = $file->extension();
        if (! array_key_exists($ext, static::$decoders))
            throw new \Exception("Unknown configuration file extension '{$ext}'");
        $decoderClass = static::$decoders[$ext];
        $decoder = new $decoderClass;
        return $decoder->decode($file->content());
    }
}
