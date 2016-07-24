<?php namespace Tarsana\Application\Templates;

use Tarsana\Application\Interfaces\TemplateInterface;

/**
 * Twig Template
 */
class TwigTemplate implements TemplateInterface {

    /**
     * Twig template instance.
     *
     * @var Twig_TemplateInterface
     */
    protected $twig;

    /**
     * Data to pass to the template.
     *
     * @var array
     */
    protected $data;

    public function __construct (\Twig_TemplateInterface $twig)
    {
        $this->twig = $twig;
        $this->data = [];
    }

    /**
     * Binds data to the template.
     *
     * @param  array $data
     * @return self
     */
    public function bind ($data)
    {
        $this->data = array_merge($this->data, $data);
        return $this;
    }

    /**
     * Renders the template.
     *
     * @return string
     */
    public function render($data = null)
    {
        return $this->twig->render($this->data);
    }

    /**
     * Clears the data.
     *
     * @return self
     */
    public function clean ()
    {
        $this->data = [];
    }
}
