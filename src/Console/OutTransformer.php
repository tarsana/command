<?php namespace Tarsana\Command\Console;

use Tarsana\Command\Interfaces\Console\TransformerInterface;

class OutTransformer implements TransformerInterface {

    const CSI = "\033[";

    const CONTROLS = [
        'up'    => '$2A',
        'down'  => '$2B',
        'right' => '$2C',
        'left'  => '$2D',
        'nextLine' => '$2E',
        'prevLine' => '$2F',
        'column'   => '$2G',

        'clearBefore' => '1J',
        'clearAfter'  => 'J',
        'clearLine'   => '2K',
        'clearAll'    => '3J',
        'clear'       => '2J',

        'color' => '38;5;$2m',
        'background' => '48;5;$2m',
        'reset' => '0m',
        'bold'  => '1m',
        'underline' => '4m'
    ];

    protected $aliases = [];

    public function alias(string $name, string $value) {
        $this->aliases[$name] = $value;
        return $this;
    }

    public function transform(string $text) : string
    {
        foreach ($this->aliases as $name => $value) {
            $text = str_replace($name, $value, $text);
        }

        foreach (self::CONTROLS as $name => $value) {
            $text = preg_replace("/<{$name}(:([^>]*))?>/", self::CSI . $value, $text);
        }

        return $text;
    }
}
