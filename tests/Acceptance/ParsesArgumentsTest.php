<?php namespace Tarsana\Command\Tests\Acceptance;

use Tarsana\Command\Command as C;
use Tarsana\Tester\CommandTestCase;

class ParsesArgumentsTest extends CommandTestCase {

    public function test_no_args_no_options() {
        $this->command(C::create())
        ->argsEqual(null)
        ->optionsEqual([]);
    }

    public function test_args_options() {
        $c = C::create()
            ->syntax('name,age:(number:11)')
            ->options(['--vip','--help']);

        $this->command($c, ['Foo', '21', '--vip'])
        ->argsEqual((object) ['name' => 'Foo', 'age' => 21])
        ->optionsEqual(['--vip' => true, '--help' => false]);

        $this->command($c, ['Bar', '--vip', '--help'])
        ->argsEqual((object) ['name' => 'Bar', 'age' => 11])
        ->optionsEqual(['--vip' => true, '--help' => true]);

        $this->command($c, ['--vip'])
        ->printsError("Failed to parse <warn>''</warn> as <info>name age</info> <error>name is missing!</error>")
        ->printsError("Failed to parse <warn>''</warn> as <info>String</info>");
    }

}
