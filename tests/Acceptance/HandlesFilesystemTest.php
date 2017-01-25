<?php namespace Tarsana\Command\Tests\Acceptance;

use Tarsana\Command\Command as C;
use Tarsana\Tester\CommandTestCase;


class HandlesFilesystemTest extends CommandTestCase {

    public function test_it_reads_file_content() {
        $c = C::create(function($app) {
            $content = $app->fs()->file('test.txt')->content();
            $app->console()->line($content);
        });

        $this->fs
            ->file('test.txt', true)
            ->content('Hello World!');

        $this->command($c)
            ->prints('Hello World!');
    }

    public function test_it_lists_files_on_directory() {
        $c = C::create(function($app) {
            $dir = $app->fs()->dir('files');
            $files = $dir->fs()->find('*')->files()->asArray();
            foreach ($files as $file) {
                $app->console()->line($file->name());
            }
        });

        $this->fs->dir('files', true);
        $this->fs->file('files/text.txt', true);
        $this->fs->file('files/music.mp3', true);
        $this->fs->file('files/photo.png', true);

        $this->command($c)
            ->printsExactly("text.txt<br>music.mp3<br>photo.png<br>");
    }

    public function test_it_creates_files_and_directories() {
        $c = C::create()
            ->action(function($app) {
                $app->fs()->dir('files', true);
                $app->fs()->file('files/demo.txt', true)
                    ->content('Yo!');
            });

        $this->assertFalse($this->fs->isDir('files'));
        $this->assertFalse($this->fs->isFile('files/demo.txt'));

        $this->command($c);

        $this->assertTrue($this->fs->isDir('files'));
        $this->assertTrue($this->fs->isFile('files/demo.txt'));
        $this->assertEquals('Yo!', $this->fs->file('files/demo.txt')->content());
    }
}
