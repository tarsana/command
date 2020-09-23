<?php namespace Tarsana\Command\Examples;

use Tarsana\Command\Command;

class HelloWorld extends Command {

    protected function init ()
    {
        $this->name('Hello World')
             ->version('1.0.0-alpha')
             ->description('Shows a "Hello World" message')
             ->options(['--formal'])
             ->describe('--formal', 'Use formal "Greetings" instead of "Hello"');
    }

    protected function execute()
    {
        $greeting = $this->option('--formal') ? 'Greetings' : 'Hello';
        $this->console->out('Your name: ');
        $name = $this->console->readLine();
        $this->console->line("{$greeting} {$name}");
    }

}
