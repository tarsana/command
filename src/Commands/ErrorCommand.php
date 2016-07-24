<?php namespace Tarsana\Application\Commands;


use League\CLImate\CLImate;
use Tarsana\Application\Command;
use Tarsana\Syntax\Factory as S;


class ErrorCommand extends Command {

    public function init ()
    {
        $this
        ->isInternal(true)
        ->syntax(S::obj([
            'name' => S::string(),
            'details' => S::string()
        ], ' '));
    }

    public function handle ()
    {
        $message = 'Some Error Happened !';
        if (null !== $input) {
            switch ($input->name) {
                case 'cmd-not-found':
                    $message = "Unable to find any command with the name '{$input->details}'";
                break;
            }
        }
        $this->cli()->error($message);
        $this->app->runner()->clear();
    }

}
