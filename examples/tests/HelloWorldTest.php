<?php namespace Tarsana\Command\Examples\Tests;

use Tarsana\Command\Examples\HelloWorld;
use Tarsana\Tester\CommandTestCase;


class HelloWorldTest extends CommandTestCase {

    public function test_it_prints_hello_world()
    {
        $this->withStdin("Amine\n")
             ->command(new HelloWorld)
             ->prints("Your name:")
             ->prints("Hello Amine<br>");
    }
    
    public function test_it_uses_formal_greeting()
    {
        $this->withStdin("Amine\n")
             ->command(new HelloWorld, ['--formal'])
             ->prints("Your name:")
             ->prints("Greetings Amine<br>");
    }

    public function test_it_shows_hello_world_version()
    {
        $this->command(new HelloWorld, ['--version'])
             ->printsExactly("<info>Hello World</info> version <info>1.0.0-alpha</info><br>");
    }

}
