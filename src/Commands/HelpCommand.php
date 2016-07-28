<?php namespace Tarsana\Application\Commands;

use League\CLImate\CLImate;
use Tarsana\Application\Command;
use Tarsana\Application\Syntaxes\ApplicationInputSyntax;
use Tarsana\Functional as F;
use Tarsana\Syntax\Factory as S;
use Tarsana\Syntax\arr;

/**
 * Generates help message for application and commands.
 *
 * @command help
 * @version  1.0.0-alpha
 *
 */
class HelpCommand extends Command {

    protected $description = 'Shows help message';

    protected $syntax = '[command]';

    public function handle ()
    {
        $command = trim($this->args->command);
        if ($command == '' || ! $this->app->hasCommand($command)) {
            $this->appHelp();
        } else {
            $this->commandHelp($command);
        }
    }

    protected function appHelp ()
    {
        $flags = [];
        $cmds = [];
        foreach ($this->app->commands() as $name => $cmd) {
            if ($name == '' || $cmd->isInternal()) continue;
            if (F\startsWith('--', $name))
                $flags[] = (object) [
                    'name'        => '    <green>' . $name . '</green>',
                    'description' => $cmd->description()
                ];
            else
                $cmds[] = (object) [
                    'name'        => '    <green>' . $name . '</green>',
                    'description' => $cmd->description()
                ];
        }

        $this->cli()->green()->out($this->app->name());
        $this->cli()->inline('version ')->yellow()->out($this->app->version());
        $this->cli()->br();
        $this->cli()->out($this->app->description());
        $this->cli()->br();
        $this->cli()->yellow()->inline('Usage: ')->green()->out(new ApplicationInputSyntax);
        $this->cli()->br();
        $this->cli()->yellow()->out('Flags:');
        $pad = $this->cli()->padding(30)->char(' ');
        foreach ($flags as $flag) {
            $pad->label($flag->name)->result($flag->description);
        }
        $this->cli()->br();
        $this->cli()->yellow()->out('Commands:');
        $pad = $this->cli()->padding(30)->char(' ');
        foreach ($cmds as $cmd) {
            $pad->label($cmd->name)->result($cmd->description);
        }

    }

    protected function commandHelp ($name)
    {
        $command = $this->app->command($name);
        $this->cli()->green()->out($name)
            ->br()
            ->green()->inline('Syntax: ')
            ->yellow()->out(S::syntax()->dump($command->syntax()))
            ->br()
            ->out($command->description());
    }

}
