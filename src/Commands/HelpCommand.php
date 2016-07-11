<?php namespace Tarsana\Application\Commands;

use Tarsana\Application\IO;


class HelpCommand extends Command {

    public function handle (IO $io, $input = null)
    {
        $io->out()->writeLine('Help message is under construction ...');
    }

}
