<?php namespace Tarsana\Command\Template;

use Tarsana\Command\Interfaces\Template\TemplateInterface;
use Tarsana\Command\Interfaces\Template\TemplateLoaderInterface;
use Tarsana\IO\Filesystem;


/**
 * This is the all-in-one template loader, it can decide
 * which template loader implementation to call based on
 * the file extension.
 */
class TemplateLoader implements TemplateLoaderInterface {

    /**
     * Template loader providers classes.
     *
     * @var array
     */
    protected static $providers = [
        'twig' => 'Tarsana\Command\Template\Twig\TwigLoader'
    ];

    /**
     * Filesystem of the templates.
     *
     * @var Tarsana\IO\Interfaces\Filesystem
     */
    protected $fs;

    /**
     * Array of template loaders.
     *
     * @var array
     */
    protected $loaders;

    public function __construct(string $templatesPath, string $cachePath = null)
    {
        $this->init($templatesPath, $cachePath);
    }

    /**
     * Initialize the loader.
     *
     * @param  string $templatesPath
     * @param  string $cachePath
     * @return self
     */
    public function init(string $templatesPath, string $cachePath = null) : TemplateLoaderInterface
    {

        $this->fs = new Filesystem($templatesPath);
        $this->loaders = [];

        foreach (self::$providers as $ext => $provider) {
            $this->loaders[$ext] = new $provider();
            $this->loaders[$ext]->init($templatesPath, $cachePath);
        }

        return $this;
    }

    /**
     * Load a template by name. The name is the relative
     * path of the template file from the templates folder
     * The name is given without extension; Exceptions are
     * thrown if no file with supported extension is found
     * or if many exists.
     *
     * @param  string $name
     * @return Tarsana\Command\Interfaces\TemplateInterface
     * @throws Tarsana\Command\Exceptions\TemplateNotFound
     * @throws Tarsana\Command\Exceptions\TemplateNameConflict
     */
    public function load (string $name) : TemplateInterface
    {
        $supportedExtensions = array_keys(self::$providers);

        $fsPathLength = strlen($this->fs->path());

        $files = $this->fs
            ->find("{$name}.*")
            ->files()
            ->asArray();

        $found = [];
        foreach ($files as $file) {
            $ext = $file->extension();
            if (!in_array($ext, $supportedExtensions))
                continue;
            $found[] = [
                'name' => substr($file->path(), $fsPathLength),
                'extension' => $ext
            ];
        }

        if (count($found) == 0) {
            throw new \InvalidArgumentException("Unable to find template with name '{$name}' on '{$this->fs->path()}'");
        }

        if (count($found) > 1) {
            throw new \InvalidArgumentException("Mutiple templates found for the name '{$name}' on '{$this->fs->path()}'");
        }

        return $this->loaders[$found[0]['extension']]->load($found[0]['name']);
    }
}
