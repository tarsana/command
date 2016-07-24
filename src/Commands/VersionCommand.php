<?php namespace Tarsana\Application\Commands;

use League\CLImate\CLImate;
use Tarsana\Application\Command;


class VersionCommand extends Command {

    protected $description = 'Shows the version of the application';

    public function handle ()
    {
        $this->cli()->green()->inline($this->app->name())
            ->inline(' version ')
            ->yellow()->out($this->app->version());
    }

}
