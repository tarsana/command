<?php namespace Tarsana\Command\Examples\Tests;

use Tarsana\Command\Examples\RenderCommand;
use Tarsana\Tester\CommandTestCase;


class RenderCommandTest extends CommandTestCase {

    public function test_it_renders_the_template()
    {
        $this->command(new RenderCommand)
             ->printsExactly("Hello You<br>");
    }

    public function test_it_renders_with_custom_name()
    {
        $this->command(new RenderCommand, ['Foo'])
             ->printsExactly("Hello Foo<br>");
    }
}
