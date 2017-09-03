<?php namespace Tarsana\Command\Interfaces\Console;

use Tarsana\IO\Interfaces\Resource\Reader;
use Tarsana\IO\Interfaces\Resource\Writer;

interface ConsoleInterface {
    // Setters/Getters
    public function stdin(Reader $in = null);
    public function stdout(Writer $out = null);
    public function stderr(Writer $err = null);
    public function outTransformer(TransformerInterface $transformer);

    // Writing
    public function out(string $text) : ConsoleInterface;
    public function line(string $text) : ConsoleInterface;
    public function error(string $text) : ConsoleInterface;

    // Reading
    public function read() : string;
    public function readLine() : string;
}
