<?php namespace Tarsana\Tester;

use PHPUnit\Framework\TestCase;
use Tarsana\Command\Command;
use Tarsana\Command\Console\Console;
use Tarsana\IO\Filesystem;
use Tarsana\IO\Filesystem\Adapters\Memory;
use Tarsana\IO\Resource\Buffer;
use Tarsana\Tester\Mocks\Transformer;

class CommandTestCase extends TestCase {

    protected $fs;
    protected $cmd;
    protected $stdin;
    protected $stdout;
    protected $stderr;

    public function setUp()
    {
        $adapter = new Memory;
        $adapter->mkdir('.', 0777, true);
        $this->fs = new Filesystem('.', $adapter);
    }

    protected function command(
        Command $command, array $args = [],
        array $options = [], bool $rawArgs = true
    ) {
        $stdin = new Buffer;
        $stdin->write($this->stdin);
        $console = (new Console)
            ->stdin($stdin)
            ->stdout(new Buffer)
            ->stderr(new Buffer)
            ->outTransformer(new Transformer);

        $this->fs->dir($command->fs()->path(), true);

        $command->console($console)
                ->fs($this->fs)
                ->run($args, $options, $rawArgs);

        $this->cmd    = $command;
        $this->stdout = $console->stdout()->read();
        $this->stderr = $console->stderr()->read();

        return $this;
    }

    protected function prints(string $text)
    {
        $this->assertTrue(
            false !== strpos($this->stdout, $text),
            "Failed asserting that '{$this->stdout}' Contains '{$text}'"
        );
        return $this;
    }

    protected function printsExactly(string $text)
    {
        $this->assertEquals($text, $this->stdout);
        return $this;
    }

    protected function printsError(string $text)
    {
        $this->assertTrue(
            false !== strpos($this->stderr, $text),
            "Failed asserting that '{$this->stderr}' Contains '{$text}'"
        );
        return $this;
    }

    protected function argsEqual($args)
    {
        $this->assertEquals($args, $this->cmd->args());
        return $this;
    }

    protected function optionsEqual(array $options)
    {
        $this->assertEquals($options, $this->cmd->options());
        return $this;
    }

    protected function withStdin(string $text) {
        $this->stdin = $text;
        return $this;
    }

    protected function havingFile(string $path, string $content = '')
    {
        $this->fs->file($path, true)->content($content);
        return $this;
    }

    protected function havingDir(string $path)
    {
        $this->fs->dir($path, true);
        return $this;
    }

}
