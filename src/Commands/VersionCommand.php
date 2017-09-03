<?php namespace Tarsana\Command\Commands;

use Tarsana\Command\SubCommand;

class VersionCommand extends SubCommand {

    protected function init()
    {
        $this->name('Version')
             ->description('Shows the version');
    }

    protected function execute()
    {
        $command = $this->parent();
        $this->console()->line("<info>{$command->name()}</info> version <info>{$command->version()}</info>");
    }
}
