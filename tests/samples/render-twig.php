<?php
require __DIR__.'/../../vendor/autoload.php';

use Tarsana\Command\Command;
use Tarsana\Command\Templates\TwigTemplateLoader;


class RenderTwigCommand extends Command {

    protected function init ()
    {
        $this
            ->name('Render Twig Sample')
            ->description('Renders a simple twig template')
            ->templatePaths(__DIR__.'/templates');
    }

    protected function execute()
    {
        $this->console->out($this->template('hello')->render([
            'name' => 'Universe'
        ]));
    }

}

(new RenderTwigCommand)->run();
