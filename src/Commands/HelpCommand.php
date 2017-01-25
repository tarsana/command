<?php namespace Tarsana\Command\Commands;

use Tarsana\Command\SubCommand;
use Tarsana\Syntax\ArraySyntax;
use Tarsana\Syntax\ObjectSyntax;
use Tarsana\Syntax\OptionalSyntax;
use Tarsana\Syntax\Syntax;

class HelpCommand extends SubCommand {

    protected function execute()
    {
        $c = $this->console();
        $c->line("<info>{$this->parent()->name()}</info> version <info>{$this->parent()->version()}</info>");
        $c->line("<br>{$this->parent()->description()}<br>");

        $syntax = $this->parent()->syntax();
        if ($syntax) {
            $c->line("Syntax: <success>[options] " . $this->formatSyntax($syntax) . "</success>");
            $c->line("Arguments:");
            foreach ($syntax->fields() as $name => $s) {
                $this->printField($name, $s);
            }
        }

        $options = array_keys($this->parent()->options());
        if (!empty($options)) {
            $c->line("Options:");
            foreach ($options as $name) {
                $description = $this->parent()->describe($name);
                $c->line("<tab><warn>{$name}</warn> {$description}");
            }
        }
    }

    protected function formatSyntax(Syntax $s) : string
    {
        if ($s instanceof ObjectSyntax)
            return implode($s->separator(), array_keys($s->fields()));
        if ($s instanceof ArraySyntax)
            return $this->formatSyntax($s->syntax()) . $s->separator() . '...';
        if ($s instanceof OptionalSyntax)
            return $this->formatSyntax($s->syntax());

        return (string) $s;
    }

    protected function getFields(Syntax $s) : array
    {
        if ($s instanceof ObjectSyntax)
            return $s->fields();
        if ($s instanceof ArraySyntax || $s instanceof OptionalSyntax)
            return $this->getFields($s->syntax());
        return [];
    }

    protected function printField(string $name, Syntax $s, string $prefix = '', int $level = 1)
    {
        $tabs = str_repeat('<tab>', $level);
        $optional = ($s instanceof OptionalSyntax);
        if ($optional)
            $default = 'default: ' . json_encode($s->getDefault());
        else
            $default = 'required';
        $description = $this->parent()->describe($prefix.$name);
        $syntax = $this->formatSyntax($s);
        $this->console()->line("{$tabs}<warn>{$name}</warn> <success>{$syntax}</success> {$description} <info>({$default})</info>");

        $level ++;
        $prefix .= $name . '.';
        foreach ($this->getFields($s) as $field => $syntax) {
            $this->printField($field, $syntax, $prefix, $level);
        }
    }

}
