<?php namespace Tarsana\Command\Interfaces\Console;

interface TransformerInterface {

    public function transform(string $text) : string;

}
