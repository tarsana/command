<?php
require __DIR__.'/../../vendor/autoload.php';

use Tarsana\Command\Command;


class HelloCommand extends Command {

    protected function init ()
    {
        $this
            ->name('Hello')
            ->version('1.1.0')
            ->description('Shows a hello message');
    }

    protected function execute()
    {
        $name = $this->console->input('')->prompt();
        $this->console->out("Hello {$name}");
    }

}

(new HelloCommand)->run();
