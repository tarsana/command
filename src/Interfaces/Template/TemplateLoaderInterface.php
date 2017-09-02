<?php namespace Tarsana\Command\Interfaces\Template;

use Tarsana\IO\Interfaces\Filesystem as FilesystemInterface;

/**
 * Templates Loader Interface
 */
interface TemplateLoaderInterface {

    /**
     * Initialize the loader.
     *
     * @param  string $templatesPath
     * @param  string $cachePath
     * @return self
     */
    public function init(string $templatesPath, string $cachePath = null) : TemplateLoaderInterface;

    /**
     * Loads a template.
     *
     * @param  string $name
     * @return Tarsana\Command\Interfaces\Template\TemplateInterface
     */
    public function load(string $name) : TemplateInterface;
}
