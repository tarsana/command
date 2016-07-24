<?php

use League\CLImate\CLImate;
use Tarsana\Application\Application;
use Tarsana\Application\Commands\Command;
use Tarsana\Application\Commands\ErrorCommand;
use Tarsana\Application\Commands\HelpCommand;
use Tarsana\Application\Inputs\ApplicationInput;
use Tarsana\Application\Runner;
use Tarsana\Application\Syntaxes\ApplicationInputSyntax;
use \Mockery as m;

class ApplicationTest extends \Codeception\TestCase\Test
{
    protected $app;

    protected function _before()
    {
        $this->app = (new Application)
            ->name('Test App')
            ->description('Just for test')
            ->version('1.0.1')
            ->runner(m::mock(Runner::class)->shouldReceive('schedule', 'start')->getMock());
    }

    protected function _after()
    {
        unset($this->app);
        m::close();
    }

    public function test_constructor()
    {
        $app = new Application;
        $this->assertEquals('Unknown', $app->name());
        $this->assertEquals('1.0.0', $app->version());
        $this->assertEquals('', $app->description());
        $this->assertEquals(6, count($app->commands()));
        $this->assertEquals(
            ['', 'help', '--help', 'error', 'version', '--version'],
            array_keys($app->commands())
        );
    }

    public function test_getters_setters()
    {
        $this->assertEquals('Test App', $this->app->name());
        $this->assertEquals('Just for test', $this->app->description());
        $this->assertEquals('1.0.1', $this->app->version());
        $cmd = m::mock(Command::class)
            ->shouldReceive('app', 'init')
            ->shouldReceive('me')
            ->andReturn('yeah')
            ->getMock();
        $this->app->command('test', $cmd);
        $this->assertEquals('yeah', $this->app->command('test')->me());
    }

    public function test_schedule_command()
    {
        $runner = m::mock(Runner::class)
            ->shouldReceive('schedule')
            ->once()
            ->andReturnUsing(function(Command $command, CLImate $cli, $input = null) {
                $this->assertEquals('yeah', $command->me());
                $this->assertEquals("it's me", $cli->me());
                $this->assertEquals('args...', $input);
            })
            ->shouldReceive('start')
            ->never()
            ->getMock();

        $cmd = m::mock(Command::class)
            ->shouldReceive('app')->once()
            ->shouldReceive('init')->once()
            ->shouldReceive('me')->andReturn('yeah')
            ->getMock();

        $cli = m::mock(CLImate::class)
            ->shouldReceive('me')
            ->andReturn("it's me")
            ->getMock();

        $this->app
            ->runner($runner)
            ->command('cmd', $cmd);

        $this->app->scheduleCommand('cmd', $cli, 'args...');
    }

    public function test_schedule_missing_command()
    {
        $runner = m::mock(Runner::class)
            ->shouldReceive('schedule')
            ->once()
            ->andReturnUsing(function(Command $command, CLImate $cli, $input = null) {
                $this->assertTrue($command instanceof ErrorCommand);
            })
            ->shouldReceive('start')
            ->never()
            ->getMock();


        $cmd = m::mock(Command::class)
            ->shouldReceive('app')->never()
            ->shouldReceive('init')->never()
            ->getMock();

        $cli = m::mock(CLImate::class);

        $this->app
            ->runner($runner)
            ->scheduleCommand('cmd', $cli, 'args...');
    }

    public function test_run_default_command()
    {
        $runner = m::mock(Runner::class)
            ->shouldReceive('schedule')
            ->once()
            ->andReturnUsing(function(Command $command, CLImate $cli, $input = null) {
                $this->assertEquals('yeah', $command->me());
            })
            ->shouldReceive('start')
            ->once()
            ->getMock();

        $cmd = m::mock(Command::class)
            ->shouldReceive('app')->once()
            ->shouldReceive('init')->once()
            ->shouldReceive('me')->andReturn('yeah')
            ->getMock();

        $this->app
            ->runner($runner)
            ->command('', $cmd)
            ->run('');
    }

    public function test_run_command_with_flag()
    {
        $runner = m::mock(Runner::class)
            ->shouldReceive('schedule')
            ->twice()
            ->andReturnUsing(function(Command $command, CLImate $cli, $input = null) {
                $this->assertTrue($command instanceof HelpCommand);
            })
            ->shouldReceive('start')
            ->twice()
            ->getMock();

        $this->app
            ->runner($runner)
            ->run('--help');

        $this->app
            ->runner($runner)
            ->run('--help bla --bla blaa');
    }

    public function test_run_custom_command()
    {
        $runner = m::mock(Runner::class)
            ->shouldReceive('schedule')
            ->once()
            ->andReturnUsing(function(Command $command, CLImate $cli, $input = null) {
                $this->assertEquals('yeah', $command->me());
                $this->assertEquals("it's me", $cli->me());
                $this->assertEquals('args...', $input);
            })
            ->shouldReceive('start')
            ->once()
            ->getMock();


        $cmd = m::mock(Command::class)
            ->shouldReceive('app')->once()
            ->shouldReceive('init')->once()
            ->shouldReceive('me')
            ->andReturn('yeah')
            ->getMock();

        $cli = m::mock(CLImate::class)
            ->shouldReceive('me')
            ->andReturn("it's me")
            ->getMock();

        $syntax = m::mock(ApplicationInputSyntax::class)
            ->shouldReceive('parse')
            ->andReturn(
                m::mock(ApplicationInput::class)
                ->shouldReceive('flags')
                ->andReturn([])
                ->shouldReceive('command')
                ->andReturn('cmd')
                ->shouldReceive('args')
                ->andReturn('args...')
                ->getMock()
            )->getMock();

        $this->app
            ->runner($runner)
            ->command('cmd', $cmd);

        $this->app->run('cmd args...', $cli, $syntax);
    }

}
