<?php
require __DIR__.'/../../vendor/autoload.php';

use Tarsana\Command\Command;
use Tarsana\Command\Environment;


class HelloCommand extends Command {

    protected function init ()
    {
        $this
            ->name('Hello World Sample')
            ->version('1.1.0')
            ->description('Shows a hello message');
    }

    protected function execute()
    {
        $this->console->inline("Hello ");
        $this->call('say', 'Happy');
        $this->console->out(" World");
    }

}

class SayCommand extends Command {

    protected function init ()
    {
        $this
            ->name('Say Sample')
            ->version('1.1.0')
            ->description('Says something')
            ->syntax('sentence');
    }

    protected function execute()
    {
        $this->console->inline($this->args->sentence);
    }

}

Environment::get()
    ->command('hello', 'HelloCommand')
    ->command('say', 'SayCommand');

(new HelloCommand)->run();
