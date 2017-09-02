<?php namespace Tarsana\Command\Tests\Acceptance;

use Tarsana\Command\Command as C;
use Tarsana\Tester\CommandTestCase;

class PrintsToConsoleTest extends CommandTestCase {

    public function test_prints_hello_world() {
        $this->command(
            C::create(function($app) {
                $app->console()->line("Hello World");
            })
        )
        ->prints("Hello")
        ->prints("World")
        ->printsExactly("Hello World<br>");
    }

}
