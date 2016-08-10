<?php namespace Tarsana\Command\Templates;

use Tarsana\Command\Interfaces\TemplateLoaderInterface;
use Tarsana\Command\Templates\TwigTemplate;

/**
 * Twig Templates Loader
 */
class TwigTemplateLoader implements TemplateLoaderInterface {

    /**
     * Twig Environment.
     *
     * @var \Twig_Environment
     */
    protected $env;

    /**
     * Initialize the loader.
     *
     * @param  string $templatesPath
     * @param  string $cachePath
     * @return self
     */
    public function init($templatesPath, $cachePath = null)
    {
        $loader = new \Twig_Loader_Filesystem($templatesPath);
        if (null !== $cachePath) {
            $this->env = new \Twig_Environment($loader, [
                'cache' => $cachePath,
            ]);
        } else {
            $this->env = new \Twig_Environment($loader);
        }
    }

    /**
     * Loads a template.
     *
     * @param  string $name
     * @return Tarsana\Command\Templates\TwigTemplate
     */
    public function load($name)
    {
        return new TwigTemplate($this->env->loadTemplate($name));
    }

}
