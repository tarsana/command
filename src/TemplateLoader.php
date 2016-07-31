<?php namespace Tarsana\Command;

use Tarsana\Command\Exceptions\TemplateNameConflict;
use Tarsana\Command\Templates\TwigTemplateLoader;
use Tarsana\Functional\Stream as s;
use Tarsana\Functional as F;
use Tarsana\IO\Filesystem;


class TemplateLoader {

    /**
     * Template Loader Providers Classes.
     *
     * @var array
     */
    protected static $providers = [
        'twig' => TwigTemplateLoader::class
    ];

    /**
     * Filesystem of the templates.
     *
     * @var Tarsana\IO\Filesystem
     */
    protected $fs;

    /**
     * Array of template loaders.
     *
     * @var array
     */
    protected $loaders;

    /**
     * Creates a new templates loader.
     *
     * @param Filesystem $fs
     */
    public function __construct(Filesystem $fs)
    {
        $this->fs = $fs;

        $this->loaders = [];

        foreach (self::$providers as $ext => $provider) {
            $this->loaders[$ext] = new $provider($this->fs->path());
        }
    }

    /**
     * Filesystem getter and setter.
     *
     * @param  Tarsana\IO\Filesystem|void $value
     * @return Tarsana\IO\Filesystem|self
     */
    public function fs (Filesystem $value = null)
    {
        if (null === $value)
            return $this->fs;
        $this->fs = $value;
        return $this;
    }

    /**
     * Load a template by name.
     *
     * @param  string $name
     * @return Tarsana\Command\Interfaces\TemplateInterface
     * @throws Tarsana\Command\Exceptions\TemplateNotFound
     * @throws Tarsana\Command\Exceptions\TemplateNameConflict
     */
    public function load ($name)
    {
        $suportedExtensions = array_keys(self::$providers);

        $fsPathLength = strlen($this->fs->path());

        $files = s::of($this->fs->find("{$name}.*"))
            ->call('files')
            ->call('asArray')
            ->filter(function($file) use($suportedExtensions) {
                return in_array($file->extension(), $suportedExtensions);
            })
            ->map(function($file) use ($fsPathLength) {
                return [
                    'name' => substr($file->path(), $fsPathLength),
                    'extension' => $file->extension()
                ];
            })
            ->get();

        if (count($files) == 0) {
            throw new TemplateNotFound("Unable to find template with name '{$name}' on '{$this->fs->path()}'");
        }
        if (count($files) > 1) {
            $names = F\toString(F\map(F\value('name'), $files));
            throw new TemplateNameConflict("Mutiple templates found for the name '{$name}': {$names}");
        }
        $file = F\head($files);
        return $this->loaders[$file['extension']]->load($file['name']);
    }
}
