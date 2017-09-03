<?php namespace Tarsana\Command\Tests\Acceptance;

use Tarsana\Command\Command as C;
use Tarsana\Command\SubCommand;
use Tarsana\Tester\CommandTestCase;


class CanHaveSubCommandsTest extends CommandTestCase {

    public function test_it_calls_subcommand() {
        $c = C::create();
        $subCommand = (new SubCommand($c))
            ->action(function($app) {
                $app->console()->line("Hey!");
            });

        $c->command('hey', $subCommand);

        $this->command($c, ['hey'])
            ->printsExactly("Hey!<br>");
    }

}
