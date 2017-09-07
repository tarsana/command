<?php namespace Tarsana\Command\Commands;

use Tarsana\Command\Helpers\SyntaxHelper;
use Tarsana\Command\SubCommand;
use Tarsana\Syntax\ArraySyntax;
use Tarsana\Syntax\Factory as S;
use Tarsana\Syntax\ObjectSyntax;
use Tarsana\Syntax\OptionalSyntax;
use Tarsana\Syntax\Syntax;

class InteractiveCommand extends SubCommand {

    const KEYS = [
        10  => 'enter',
        127 => 'backspace',
        65  => 'up',
        66  => 'down',
        67  => 'right',
        68  => 'left',
        9   => 'tab'
    ];

    protected $helper;
    protected $confirmSyntax;

    protected function init()
    {
        $this->name('Interactive')
             ->description('Reads the command arguments and options interactively.');
        $this->helper = SyntaxHelper::instance();
        $this->confirmSyntax = S::optional(S::boolean(), false);
    }

    protected function execute()
    {
        $parent = $this->parent;
        $syntax = $parent->syntax();
        $this->console->out('<save>');

        if ($syntax) {
            $args = $this->read($syntax);
            $parent->args($args);
        }

        $options = array_keys($parent->options());
        $chosen = [];
        foreach($options as $option) {
            $bool = $this->read($this->confirmSyntax, $option, true);
            $parent->options[$option] = $bool;
            if ($bool) {
                $chosen[] = $option;
            }
        }

        $options = implode(' ', $chosen) . ' ';
        $args = $syntax ? $syntax->dump($args) : '';

        $this->console->out('<load><clearAfter>');
        $this->console->line("> {$options}{$args}<br>");

        return $this->parent->fire();
    }

    protected function read(Syntax $syntax, string $prefix = '', bool $display = false)
    {
        if ($display) {
            $this->display($syntax, $prefix);
        }

        $type = $this->helper->type($syntax);
        $result = null;
        switch ($type) {
            case 'object':
                $result = $this->readObject($syntax, $prefix);
            break;
            case 'array':
                $result = $this->readArray($syntax, $prefix);
            break;
            case 'optional':
                $result = $this->readOptional($syntax, $prefix);
            break;
            default:
                $result = $this->readSimple($syntax);
            break;
        }

        return $result;
    }

    protected function display(Syntax $syntax, string $name)
    {
        $text = $this->helper->asString($syntax);
        $default = '';
        if ($syntax instanceof OptionalSyntax) {
            $default = '(default: ' . json_encode($syntax->getDefault()) . ')';
        }
        $description = $this->parent->describe($name);
        $this->console->out(
            "<success>{$name}</success> <warn>{$text}</warn>"
          . " {$description} <warn>{$default}</warn><br>"
        );
    }

    protected function readObject(ObjectSyntax $syntax, string $prefix)
    {
        $result = [];
        if ($prefix != '')
            $prefix .= '.';
        foreach ($syntax->fields() as $name => $s) {
            $fullname = $prefix . $name;
            $result[$name] = $this->read($s, $fullname, true);
        }
        return (object) $result;
    }

    protected function readArray(ArraySyntax $syntax, string $prefix)
    {
        $result = [];
        $repeat = true;
        while ($repeat) {
            $result[] = $this->read($syntax->syntax(), $prefix);
            $this->console->out("Add new item to <success>{$prefix}</success>?<br>");
            $repeat = $this->readOptional($this->confirmSyntax, '');
            $this->clearLines(3);
        }
        return $result;
    }

    protected function readOptional(OptionalSyntax $syntax, string $prefix)
    {
        $default = $syntax->syntax()->dump($syntax->getDefault());
        $this->console->out("<color:252>{$default}<column:1><reset>");
        $n = ord($this->console->char());
        $this->console->out('<column:1><clearLine>');
        if (array_key_exists($n, static::KEYS) && static::KEYS[$n] == 'enter')
            return $syntax->getDefault();
        return $this->read($syntax->syntax(), $prefix);
    }

    protected function readSimple(Syntax $syntax)
    {
        $this->console->out('<column:1>> ');
        $done = false;
        $text = '';
        $result = null;
        while (! $done) {
            $c = $this->readChar();
            switch($c) {
                case 'enter':
                    $done = true;
                break;
                case 'backspace':
                    $text = substr($text, 0, -1);
                break;
                default:
                    $text .= $c;
                break;
            }

            try {
                $result = $syntax->parse($text);
                $this->clearLines(1);
                $this->console->out("> {$text}");
            } catch (\Exception $e) {
                $this->clearLines(1);
                $this->console->out("> <warn>{$text}</warn>");
                $done = false;
            }
        }
        $this->console->out('<br>');

        return $result;
    }

    protected function readChar() : string
    {
        $c = $this->console->char();
        if (ctype_print($c))
            return $c;
        $n = ord($c);
        if (
            array_key_exists($n, static::KEYS)
            && in_array(static::KEYS[$n], ['enter', 'backspace'])
        ) {
            return static::KEYS[$n];
        }
        return '';
    }

    protected function clearLines(int $number)
    {
        $text = '<clearLine>'
            . str_repeat('<prevLine><clearLine>', $number - 1)
            . '<column:1>';
        $this->console->out($text);
    }
}
