<?php namespace Tarsana\Command\Interfaces;

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
    public function init($templatesPath, $cachePath = null);

    /**
     * Loads a template.
     *
     * @param  string $name
     * @return Tarsana\Command\Interfaces\TemplateInterface
     */
    public function load($name);

}
