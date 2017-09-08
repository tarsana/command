<?php namespace Tarsana\Command\Tests\Acceptance;

use Tarsana\Command\Command as C;
use Tarsana\Tester\CommandTestCase;

class ReadsArgumentsInteractivelyTest extends CommandTestCase {

    public function test_no_args_no_options() {
        $this->command(C::create(), ['-i'])
        ->argsEqual(null)
        ->optionsEqual([]);
    }

    public function test_args_options() {
        $c = C::create()
            ->syntax('name, age:(number:11), friends: [string]')
            ->options(['--vip','--help']);

        $this->withStdin("Foo\n\nBar\n y\nBaz\n n\n y\n\n")
             ->command($c, ['-i'])
             ->argsEqual((object) [
                'name' => 'Foo',
                'age' => 11,
                'friends' => ['Bar', 'Baz']
             ])
             ->optionsEqual(['--vip' => true, '--help' => false]);
    }

}
