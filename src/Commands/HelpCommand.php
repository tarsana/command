<?php namespace Tarsana\Command\Commands;

use Tarsana\Command\Command;
use Tarsana\Command\SubCommand;
use Tarsana\Syntax\Factory as S;
use Tarsana\Functional as F;


class HelpCommand extends SubCommand {

    protected function init ()
    {
        $this
            ->name('Help')
            ->version('0.0.1')
            ->description('Shows help message')
            ->syntax('[command]');
    }

    protected function execute ()
    {
        $command = trim($this->args->command);
        if (empty($command) || !$this->parent()->hasCommand($command))
            $this->showHelpOf($this->parent);
        else
            $this->showHelpOf($this->parent->command($command));
    }

    protected function showHelpOf (Command $command)
    {
        $c = $this->console();

        $c->br()
          ->green()
          ->inline($command->name() . ' ')
          ->yellow()
          ->out($command->version())
          ->br()
          ->out($command->description())
          ->br()
          ->yellow()
          ->out('Syntax: ')
          ->tab()->out(S::syntax()->dump($command->syntax()))
          ->br();

        if (!empty($command->subCommands())) {
            $c->yellow()->out('Subcommands:');
            $padding = F\s(array_keys($command->subCommands()))
                ->map('strlen')
                ->reduce(function($result, $item) {
                    return ($result > $item) ? $result : $item;
                }, 0)
                ->get() * 2;
            foreach ($command->subCommands() as $name => $cmd) {
                $c->tab()
                  ->padding($padding)->char(' ')
                  ->label("<green>{$name}</green>")
                  ->result($cmd->description());
            }
        }
    }
}
