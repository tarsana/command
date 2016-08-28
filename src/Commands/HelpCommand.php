<?php namespace Tarsana\Command\Commands;

use Tarsana\Syntax\Syntax;
use Tarsana\Command\Command;
use Tarsana\Functional as F;
use Tarsana\Command\SubCommand;
use Tarsana\Syntax\ArraySyntax;
use Tarsana\Syntax\ObjectSyntax;

/**
 * The default help subcommand
 */
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

    /**
     * Shows the help of a specific command.
     *
     * @param  Command $command
     * @return void
     */
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
          ->inline('Arguments: ');

        $args = F\s(array_keys($command->syntax()->fields()))
            ->then(F\join(' '))
            ->get();
        if ('' == trim($args)) {
            $c->white()->out('none');
        } else {
            $c->white()->out($args);

            foreach ($command->syntax()->fields() as $name => $syntax) {
                $this->showSyntax($name, $syntax, 2);
            }

            $c->br();
        }

        if (!empty($command->subCommands)) {
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

    /**
     * Shows the syntax description of a part of arguments.
     *
     * @param  string $name
     * @param  Tarsana\Syntax\Syntax $syntax
     * @param  int $level
     * @return void
     */
    protected function showSyntax($name, Syntax $syntax, $level)
    {
        $type = $this->typeOf($syntax);
        $c = $this->console->inline(str_repeat(' ', $level))
            ->green()->inline("{$name} ")
            ->yellow()->inline("[{$type}] ")
            ->white()->inline("{$syntax->description()} ")
            ->yellow();
        if ($syntax->isRequired()) {
            $c->out("(Required)");
        } else {
            $c->inline("(default: ")
              ->white()->inline(json_encode($syntax->getDefault()))
              ->yellow()->out(" )");
        }

        if ($syntax instanceof ArraySyntax) {
            $syntax = $syntax->itemSyntax();
        }

        if ($syntax instanceof ObjectSyntax) {
            $level += 2;
            foreach ($syntax->fields() as $n => $s) {
                $this->showSyntax($n, $s, $level);
            }
        }
    }

    /**
     * Retuns a string representation of the given syntax.
     * {:,name,#age}    -> name:age
     * {:,name,#age}[,] -> name:age,...
     *
     * @param  Syntax $syntax
     * @return string
     */
    protected function typeOf(Syntax $syntax)
    {
        if ($syntax instanceof ObjectSyntax) {
            return F\join($syntax->separator(), array_keys($syntax->fields()));
        }

        if ($syntax instanceof ArraySyntax) {
            return $this->typeOf($syntax->itemSyntax()) . $syntax->separator() . '...';
        }

        return "{$syntax}";
    }
}
