<?php

use Tarsana\Application\Commands\Command;
use Tarsana\Application\IO;
use Tarsana\Application\Runner;
use \Mockery as m;

class RunnerTest extends \Codeception\TestCase\Test
{
    protected $runner;

    protected function _before()
    {
        $this->runner = new Runner;
    }

    protected function _after()
    {
        m::close();
    }

    public function test_schedule_and_run_one_command()
    {
        $cmd = m::mock(Command::class)
            ->shouldReceive('run')->once()
            ->getMock();
        $io = m::mock(IO::class);

        $this->runner->schedule($cmd, $io)
            ->start();
        $this->runner->start();
    }

    public function test_schedule_and_run_multiple_commands()
    {
        $cmd = m::mock(Command::class)
            ->shouldReceive('run')->times(3)
            ->getMock();
        $io = m::mock(IO::class);

        $this->runner
            ->schedule($cmd, $io)
            ->schedule($cmd, $io)
            ->schedule($cmd, $io)
            ->start();
    }

    public function test_run_command_that_schedules_other_commands()
    {
        $cmd = m::mock(Command::class)
            ->shouldReceive('run')->times(2)
            ->getMock();
        $io = m::mock(IO::class);

        $addingCmd = m::mock(Command::class)
            ->shouldReceive('run')->once()
            ->andReturnUsing(function() use ($cmd, $io){
                $this->runner->schedule($cmd, $io);
            })
            ->getMock();

        $this->runner
            ->schedule($addingCmd, $io)
            ->schedule($cmd, $io)
            ->start();
    }

    public function test_run_command_that_clears_other_commands()
    {
        $cmd = m::mock(Command::class)
            ->shouldReceive('run')->never()
            ->getMock();
        $io = m::mock(IO::class);

        $clearingCmd = m::mock(Command::class)
            ->shouldReceive('run')->once()
            ->andReturnUsing(function(){
                $this->runner->clear();
            })
            ->getMock();

        $this->runner
            ->schedule($clearingCmd, $io)
            ->schedule($cmd, $io)
            ->schedule($cmd, $io)
            ->start();
    }

}
