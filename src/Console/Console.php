<?php namespace Tarsana\Command\Console;

use Tarsana\Command\Console\OutTransformer;
use Tarsana\Command\Interfaces\Console\ConsoleInterface;
use Tarsana\Command\Interfaces\Console\TransformerInterface;
use Tarsana\IO\Interfaces\Resource\Reader as ReaderInterface;
use Tarsana\IO\Interfaces\Resource\Writer as WriterInterface;
use Tarsana\IO\Resource\Reader;
use Tarsana\IO\Resource\Writer;
use Tarsana\Syntax\Syntax;

class Console implements ConsoleInterface {

    protected $out;
    protected $in;
    protected $err;
    protected $outTransformer;

    public function __construct()
    {
        $this->out = new Writer;
        $this->err = new Writer('php://stderr');
        $this->in  = new Reader;
        $this->outTransformer = new OutTransformer;
        $aliases = [
            '<info>'  => '<color:33>',
            '</info>' => '<reset>',
            '<warn>'  => '<color:220>',
            '</warn>' => '<reset>',
            '<success>'  => '<color:46>',
            '</success>' => '<reset>',
            '<error>' => '<bold><color:15><background:124>',
            '</error>' => '<reset>',
            '<tab>' => '    ',
            '<br>'  => PHP_EOL
        ];
        foreach ($aliases as $name => $value) {
            $this->outTransformer()->alias($name, $value);
        }
    }

    public function stdin(ReaderInterface $in = null)
    {
        if (null === $in)
            return $this->in;
        $this->in = $in;
        return $this;
    }

    public function stdout(WriterInterface $out = null)
    {
        if (null === $out)
            return $this->out;
        $this->out = $out;
        return $this;
    }

    public function stderr(WriterInterface $err = null)
    {
        if (null === $err)
            return $this->err;
        $this->err = $err;
        return $this;
    }

    public function outTransformer(TransformerInterface $value = null)
    {
        if (null === $value) {
            return $this->outTransformer;
        }
        $this->outTransformer = $value;
        return $this;
    }

    public function out(string $text) : ConsoleInterface
    {
        $this->out->write($this->outTransformer()->transform($text));
        return $this;
    }

    public function line(string $text) : ConsoleInterface
    {
        return $this->out($text . '<br>');
    }

    public function error(string $text) : ConsoleInterface
    {
        $text = "<error>{$text}</error><br>";
        $this->err->write($this->outTransformer()->transform($text));
        return $this;
    }

    public function read() : string
    {
        return $this->in()->read();
    }
}
