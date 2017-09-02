<?php namespace Tarsana\Tester\Mocks;

use Tarsana\Command\Interfaces\Console\TransformerInterface;

class Transformer implements TransformerInterface {
    public function transform(string $text) : string
    {
        return $text;
    }
    public function alias(string $name, string $value) : Transformer
    {
        return $this;
    }
}
