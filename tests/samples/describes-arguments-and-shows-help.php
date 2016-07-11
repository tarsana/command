<?php
require __DIR__.'/../../vendor/autoload.php';

use Tarsana\Command\Command;

class DescribeCommand extends Command {

    protected function init ()
    {
        $this
            ->name('Describe')
            ->version('1.0.1')
            ->description('Describes arguments and shows detailed help message')
            ->syntax('name #stars [#forks] owner{name,email,followers{name,[email]}[]}')
            ->describe('name', 'The name of the repository')
            ->describe('stars', 'Number of stars of the repository')
            ->describe('forks', 'Number of forks of the repository')
            ->describe('owner', 'The owner of the repository')
            ->describe('owner.name', 'The name of the owner')
            ->describe('owner.email', 'The email of the owner')
            ->describe('owner.followers', 'The followers of the owner')
            ->describe('owner.followers.name', 'The name of the follower')
            ->describe('owner.followers.email', 'The email of the follower');
    }

    protected function execute()
    {
        $this->console->out("Please run the help sub-command !");
    }

}

(new DescribeCommand)->run();
