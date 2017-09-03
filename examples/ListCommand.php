<?php namespace Tarsana\Command\Examples;

use Tarsana\Command\Command;

class ListCommand extends Command {

    protected function init ()
    {
        $this->name('List')
             ->version('1.0.0-alpha')
             ->description('Lists files and directories in the current directory.');
    }

    protected function execute()
    {
        foreach($this->fs->find('*')->asArray() as $file) {
            $this->console->line($file->name());
        }
    }

}
