<?php namespace Tarsana\Command\Interfaces\Template;

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
    public function bind(array $data) : TemplateInterface;

    /**
     * Renders the template.
     *
     * @param  array $data
     * @return string
     */
    public function render(array $data = null) : string;

    /**
     * Removes the bound data.
     *
     * @return self
     */
    public function clean() : TemplateInterface;
}
