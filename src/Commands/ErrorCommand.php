<?php namespace Tarsana\Application\Commands;


use Tarsana\Application\IO;
use Tarsana\Syntax\Factory as S;


class ErrorCommand extends Command {

    public function init ()
    {
        $this->syntax(S::obj([
            'name' => S::string(),
            'details' => S::string()
        ], ' '));
    }

    public function handle (IO $io, $input = null)
    {
        $message = 'Some Error Happened !';
        if (null !== $input) {
            switch ($input->name) {
                case 'cmd-not-found':
                    $message = "Unable to find any command with the name '{$input->details}'";
                break;
            }
        }
        $io->err()->writeLine($message);
    }

}
