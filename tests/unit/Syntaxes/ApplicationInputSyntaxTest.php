<?php

use Tarsana\Application\Inputs\ApplicationInput;
use Tarsana\Application\Syntaxes\ApplicationInputSyntax;
use \Mockery as m;

class ApplicationInputSyntaxTest extends \Codeception\TestCase\Test
{
    protected $syntax;
    protected $cases;

    protected function _before()
    {
        $this->syntax = new ApplicationInputSyntax;
        $this->cases = [
            '' => [ 'f' => [], 'c' => null, 'a' => ''],
            '--flag' => [ 'f' => ['--flag'], 'c' => null, 'a' => ''],
            'command' => [ 'f' => [], 'c' => 'command', 'a' => ''],
            '--flag command' => [ 'f' => ['--flag'], 'c' => 'command', 'a' => ''],
            '--flag command arg1' => [ 'f' => ['--flag'], 'c' => 'command', 'a' => 'arg1'],
            '--f --g c arg1 blabla...' => [ 'f' => ['--f', '--g'], 'c' => 'c', 'a' => 'arg1 blabla...']
        ];
    }

    protected function _after()
    {
        m::close();
    }

    public function test_parse()
    {
        foreach ($this->cases as $input => $output) {
            $ai = $this->syntax->parse($input);
            $this->assertEquals($output['f'], $ai->flags());
            $this->assertEquals($output['c'], $ai->command());
            $this->assertEquals($output['a'], $ai->args());
        }
    }

    public function test_dump()
    {
        foreach ($this->cases as $input => $output) {
            $this->assertEquals($input,
                $this->syntax->dump(new ApplicationInput($output['f'], $output['c'], $output['a']))
            );
        }
    }

}
