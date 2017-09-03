<?php namespace Tarsana\Command\Tests\Acceptance;

use Tarsana\Command\Command as C;
use Tarsana\Command\Template\Twig\TwigLoader;
use Tarsana\Tester\CommandTestCase;


class RendersTemplatesTest extends CommandTestCase {

    public function test_it_renders_twig_template() {
        $c = C::create(function($app) {
            $app->templatesPath(TEMPLATES_PATH);
            $template = $app->template('hello');
            $output = $template->render(['name' => 'You']);
            $app->console()->line($output);
            $output = $template
                ->clear()
                ->bind(['name' => 'Me'])
                ->render();
            $app->console()->line($output);
        });

        $this->command($c)
            ->printsExactly("Hello You<br>Hello Me<br>");

        $c->templatesLoader(new TwigLoader(TEMPLATES_PATH, CACHE_PATH));
        $this->command($c)
            ->printsExactly("Hello You<br>Hello Me<br>");
    }

}
