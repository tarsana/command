<?php namespace Tarsana\Application;

use Tarsana\IO\Interfaces\ReaderInterface;
use Tarsana\IO\Interfaces\WriterInterface;
use Tarsana\IO\Resources\InputResource;
use Tarsana\IO\Resources\OutputResource;

/**
 * Container of input readers and output writers of an application/command.
 */
class IO {

    /**
     * Standard input reader.
     *
     * @var Tarsana\IO\Interfaces\ReaderInterface
     */
    protected $in;

    /**
     * Standard output writer.
     *
     * @var Tarsana\IO\Interfaces\WriterInterface
     */
    protected $out;

    /**
     * Error output writer.
     *
     * @var Tarsana\IO\Interfaces\WriterInterface
     */
    protected $err;

    /**
     * Log output writer.
     *
     * @var Tarsana\IO\Interfaces\WriterInterface
     */
    protected $log;

    /**
     * Creates a new IO.
     *
     * @param ReaderInterface|null $in
     * @param WriterInterface|null $out
     * @param WriterInterface|null $err
     * @param WriterInterface|null $log
     */
    public function __construct (
        ReaderInterface $in = null,
        WriterInterface $out = null,
        WriterInterface $err = null,
        WriterInterface $log = null)
    {
        if (null === $in)
            $in = new InputResource(STDIN);
        if (null === $out)
            $out = new OutputResource(STDOUT);
        if (null === $err)
            $err = new OutputResource(STDERR);
        if (null === $log)
            $log = new OutputResource(STDERR);

        $this->in = $in;
        $this->out = $out;
        $this->err = $err;
        $this->log = $log;
    }

    /**
     * Standard Input getter and setter.
     *
     * @param  Tarsana\IO\Interfaces\ReaderInterface|void $value
     * @return Tarsana\IO\Interfaces\ReaderInterface|self
     */
    public function in (ReaderInterface $value = null)
    {
        if (null === $value)
            return $this->in;
        $this->in = $value;
        return $this;
    }

    /**
     * Standard Output getter and setter.
     *
     * @param  Tarsana\IO\Interfaces\WriterInterface|void $value
     * @return Tarsana\IO\Interfaces\WriterInterface|self
     */
    public function out (WriterInterface $value = null)
    {
        if (null === $value)
            return $this->out;
        $this->out = $value;
        return $this;
    }

    /**
     * Error Output getter and setter.
     *
     * @param  Tarsana\IO\Interfaces\WriterInterface|void $value
     * @return Tarsana\IO\Interfaces\WriterInterface|self
     */
    public function err (WriterInterface $value = null)
    {
        if (null === $value)
            return $this->err;
        $this->err = $value;
        return $this;
    }

    /**
     * Log Output getter and setter.
     *
     * @param  Tarsana\IO\Interfaces\WriterInterface|void $value
     * @return Tarsana\IO\Interfaces\WriterInterface|self
     */
    public function log (WriterInterface $value = null)
    {
        if (null === $value)
            return $this->log;
        $this->log = $value;
        return $this;
    }

}
