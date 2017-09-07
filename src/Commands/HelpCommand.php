<?php namespace Tarsana\Command\Commands;

use Tarsana\Command\Helpers\SyntaxHelper;
use Tarsana\Command\SubCommand;
use Tarsana\Syntax\ArraySyntax;
use Tarsana\Syntax\ObjectSyntax;
use Tarsana\Syntax\OptionalSyntax;
use Tarsana\Syntax\Syntax;

class HelpCommand extends SubCommand {

    protected $helper;

    protected function init()
    {
        $this->name('Help')
             ->description('Shows the help message.');
        $this->helper = SyntaxHelper::instance();
    }

    protected function execute()
    {
        $parent = $this->parent;

        $text = "<info>{$parent->name}</info> version <info>{$parent->version}</info>"
              . "<br><br>{$parent->description}<br><br>"
              . $this->syntaxHelp()
              . $this->optionsHelp()
              . $this->subCommandsHelp();

        $this->console()->out($text);
    }

    protected function syntaxHelp() : string
    {
        $syntax = $this->parent->syntax();
        $helper = $this->helper;
        $text   = '';

        if ($syntax) {
            $string = $helper->asString($syntax);
            $text  .= "Syntax: <success>[options] {$string}</success><br>"
                    . "Arguments:<br>";
            foreach ($syntax->fields() as $name => $s) {
                $text .= $this->fieldHelp($name, $s);
            }
        }

        return $text;
    }

    protected function optionsHelp() : string
    {
        $options = array_keys($this->parent->options());
        $text = '';
        if (!empty($options)) {
            $text .= 'Options:<br>';
            foreach ($options as $name) {
                $description = $this->parent()->describe($name);
                $text .= "<tab><warn>{$name}</warn> {$description}<br>";
            }
        }

        return $text;
    }

    protected function subCommandsHelp() : string
    {
        $subCommands = $this->parent->commands();
        $text = '';
        if (!empty($subCommands)) {
            $text .= 'SubCommands:<br>';
            foreach ($subCommands as $name => $cmd) {
                $text .= "<tab><warn>{$name}</warn> {$cmd->description()}<br>";
            }
        }

        return $text;
    }

    protected function fieldHelp(
        string $name, Syntax $s, string $prefix = '', int $level = 1
    ) : string
    {
        $tabs = str_repeat('<tab>', $level);
        $optional = ($s instanceof OptionalSyntax);
        if ($optional)
            $default = 'default: ' . json_encode($s->getDefault());
        else
            $default = 'required';
        $description = $this->parent()->describe($prefix.$name);
        $syntax = $this->helper->asString($s);
        $text   = "{$tabs}<warn>{$name}</warn> <success>{$syntax}</success>"
                . " {$description} <info>({$default})</info><br>";
        $level ++;
        $prefix .= $name . '.';
        foreach ($this->helper->fields($s) as $field => $syntax) {
            $text .= $this->fieldHelp($field, $syntax, $prefix, $level);
        }

        return $text;
    }

}
