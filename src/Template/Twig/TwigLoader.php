<?php namespace Tarsana\Command\Template\Twig;

use Tarsana\Command\Interfaces\Template\TemplateInterface;
use Tarsana\Command\Interfaces\Template\TemplateLoaderInterface;
use Tarsana\Command\Template\Twig\TwigTemplate;

/**
 * Twig Templates Loader
 */
class TwigLoader implements TemplateLoaderInterface {

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
    public function init(string $templatesPath, string $cachePath = null) : TemplateLoaderInterface
    {
        $loader = new \Twig_Loader_Filesystem($templatesPath);
        if (null !== $cachePath) {
            $this->env = new \Twig_Environment($loader, [
                'cache' => $cachePath,
            ]);
        } else {
            $this->env = new \Twig_Environment($loader);
        }
        return $this;
    }

    /**
     * Loads a template.
     *
     * @param  string $name
     * @return Tarsana\Command\Template\Twig\TwigTemplate
     */
    public function load(string $name) : TemplateInterface
    {
        return new TwigTemplate($this->env->loadTemplate($name));
    }

}
