<?php namespace Tarsana\Command\Template\Twig;

use Tarsana\Command\Interfaces\Template\TemplateInterface;

class TwigTemplate implements TemplateInterface {

    /**
     * Twig template instance.
     */
    protected $twig;

    /**
     * Data to pass to the template.
     *
     * @var array
     */
    protected $data;

    public function __construct ($twig)
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
    public function bind (array $data) : TemplateInterface
    {
        $this->data = array_merge($this->data, $data);
        return $this;
    }

    /**
     * Renders the template.
     *
     * @return string
     */
    public function render(array $data = null) : string
    {
        if (null !== $data) {
            $this->bind($data);
        }
        return $this->twig->render($this->data);
    }

    /**
     * Clears the data.
     *
     * @return self
     */
    public function clear () : TemplateInterface
    {
        $this->data = [];
        return $this;
    }
}
