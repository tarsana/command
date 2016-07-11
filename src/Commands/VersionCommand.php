<?php namespace Tarsana\Application\Commands;

use Tarsana\Application\IO;



class VersionCommand extends Command {

    public function handle (IO $io, $input = null)
    {
        $io->out()->writeLine($this->app->version());
    }

}
