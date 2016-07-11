<?php
require __DIR__.'/../../vendor/autoload.php';

use Tarsana\Command\Command;
use Tarsana\Command\Templates\TwigTemplateLoader;


class HandleFSCommand extends Command {

    protected function init ()
    {
        $this
            ->name('Handle Filesystem Sample')
            ->description('Creates a temp file');
    }

    protected function execute()
    {
        $this->fs->file('tests/samples/files/temp.txt', true)
            ->content('I am a temp file');
        $this->console->out('Done !');
    }

}

(new HandleFSCommand)->run();
