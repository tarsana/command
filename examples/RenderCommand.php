<?php namespace Tarsana\Command\Examples;

use Tarsana\Command\Command;

class RenderCommand extends Command {

    protected function init ()
    {
        $this->name('Render')
             ->version('1.0.0')
             ->description('Renders the hello template.')
             ->syntax('name: (string:You)')
             ->describe('name', 'Your name.')
             ->templatesPath(TEMPLATES_PATH);
             // this points to /tests/resources/templates
    }

    protected function execute()
    {
        $message = $this->template('hello')
            ->render([
                'name' => $this->args->name
            ]);

        $this->console->line($message);
    }

}
