<?php namespace Tarsana\Command\Tests\Acceptance;

use Tarsana\Command\Command as C;
use Tarsana\Tester\CommandTestCase;


class RendersTemplatesTest extends CommandTestCase {

    public function test_it_renders_twig_template() {
        $c = C::create(function($app) {
            $app->templatesPath(TEMPLATES_PATH);
            $output = $app->template('hello')
                ->render(['name' => 'You']);
            $app->console()->line($output);
        });

        $this->command($c)
            ->printsExactly("Hello You<br>");
    }

}
