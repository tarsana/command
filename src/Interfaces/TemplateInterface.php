<?php namespace Tarsana\Command\Interfaces;

/**
 * Template Engine Interface
 */
interface TemplateInterface {

    /**
     * Binds data to the template.
     *
     * @param  array $data
     * @return self
     */
    public function bind($data);

    /**
     * Renders the template.
     *
     * @return string
     */
    public function render($data = null);

    /**
     * Removes the bound data.
     *
     * @return self
     */
    public function clean();
}
