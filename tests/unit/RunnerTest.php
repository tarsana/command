<?php

use League\CLImate\CLImate;
use Tarsana\Application\Commands\Command;
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
        $cli = m::mock(CLImate::class);

        $this->runner->schedule($cmd, $cli)
            ->start();
        $this->runner->start();
    }

    public function test_schedule_and_run_multiple_commands()
    {
        $cmd = m::mock(Command::class)
            ->shouldReceive('run')->times(3)
            ->getMock();
        $cli = m::mock(CLImate::class);

        $this->runner
            ->schedule($cmd, $cli)
            ->schedule($cmd, $cli)
            ->schedule($cmd, $cli)
            ->start();
    }

    public function test_run_command_that_schedules_other_commands()
    {
        $cmd = m::mock(Command::class)
            ->shouldReceive('run')->times(2)
            ->getMock();
        $cli = m::mock(CLImate::class);

        $addingCmd = m::mock(Command::class)
            ->shouldReceive('run')->once()
            ->andReturnUsing(function() use ($cmd, $cli){
                $this->runner->schedule($cmd, $cli);
            })
            ->getMock();

        $this->runner
            ->schedule($addingCmd, $cli)
            ->schedule($cmd, $cli)
            ->start();
    }

    public function test_run_command_that_clears_other_commands()
    {
        $cmd = m::mock(Command::class)
            ->shouldReceive('run')->never()
            ->getMock();
        $cli = m::mock(CLImate::class);

        $clearingCmd = m::mock(Command::class)
            ->shouldReceive('run')->once()
            ->andReturnUsing(function(){
                $this->runner->clear();
            })
            ->getMock();

        $this->runner
            ->schedule($clearingCmd, $cli)
            ->schedule($cmd, $cli)
            ->schedule($cmd, $cli)
            ->start();
    }

}
