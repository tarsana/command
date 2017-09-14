<?php namespace Tarsana\Command\Tests\Acceptance;

use Tarsana\Command\Command as C;
use Tarsana\Tester\CommandTestCase;


class LoadsConfigurationTest extends CommandTestCase {

    public function test_it_loads_configuration() {
        $c = C::create(function($app) {
            $app->configPaths(['/home/user/.config.json', 'config.json']);

            $name = $app->config('name');
            $repoURL = $app->config('repo.url');
            $app->console()->line("{$name}:{$repoURL}");
        });

        $this->fs
            ->file('/home/user/.config.json', true)
            ->content(json_encode(['name' => 'user']));

        $this->fs
            ->file('config.json', true)
            ->content(json_encode(['repo' => ['url' => 'tarsana']]));

        $this->command($c)
            ->prints('user:tarsana<br>');
    }
}
