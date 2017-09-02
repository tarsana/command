<?php namespace Tarsana\Command\Tests\Acceptance;

use Tarsana\Command\Command as C;
use Tarsana\Tester\CommandTestCase;


class ShowsVersionTest extends CommandTestCase {

    public function test_it_shows_name_and_version() {
        $c = C::create()
            ->name('Foo')
            ->version('2.0.1-beta.1.0.12');
        $this->command($c, ['--version'])
            ->printsExactly("<info>Foo</info> version <info>2.0.1-beta.1.0.12</info><br>");
    }
}
