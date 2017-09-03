<?php namespace Tarsana\Command\Examples;

use Tarsana\Command\Command;

class HelloWorld extends Command {

    protected function init ()
    {
        $this->name('Hello World')
             ->version('1.0.0-alpha')
             ->description('Shows a "Hello World" message');
    }

    protected function execute()
    {
        $this->console->out('Your name: ');
        $name = $this->console->readLine();
        $this->console->line("Hello {$name}");
    }

}
