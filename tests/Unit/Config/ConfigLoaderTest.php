<?php namespace Tarsana\Command\Tests\Unit\Config;

use PHPUnit\Framework\TestCase;
use Tarsana\Command\Config\Config;
use Tarsana\Command\Config\ConfigLoader;
use Tarsana\IO\Filesystem;
use Tarsana\IO\Filesystem\Adapters\Memory;


class ConfigLoaderTest extends TestCase {

    protected $fs;
    protected $loader;

    public function setUp(): void {
        $adapter = new Memory;
        $adapter->mkdir('.', 0777, true);
        $this->fs = new Filesystem('.', $adapter);
        $this->loader = new ConfigLoader($this->fs);
    }

    public function test_it_loads_single_config() {
        $data = ['name' => 'foo', 'repo' => 'bar'];
        $this->fs->file('config.json', true)
             ->content(json_encode($data));
        $this->assertEquals($data, $this->loader->load(['config.json'])->get());
    }

    public function test_it_loads_many_configs() {
        $data1 = ['name' => 'foo', 'repo' => 'bar'];
        $data2 = ['repo' => ['type' => 'git']];
        $data3 = ['repo' => ['name' => 'baz'], 'descr' => 'blabla'];
        $merged = ['name' => 'foo', 'repo' => ['type' => 'git', 'name' => 'baz'], 'descr' => 'blabla'];

        $this->fs->file('/opt/command/config.json', true)->content(json_encode($data1));
        $this->fs->file('/home/user/config.json', true)->content(json_encode($data2));
        $this->fs->file('config.json', true)->content(json_encode($data3));

        $this->assertEquals($merged, $this->loader->load([
            '/opt/command/config.json',
            '/home/user/config.json',
            '/projects/config.json', // this is missing
            'config.json'
        ])->get());
    }

   public function test_it_loads_empty_config_when_no_path_is_given() {
       $this->assertEquals([], $this->loader->load([])->get());
   }

    public function test_it_throws_exception_when_unknown_extension() {
        $this->expectException('Exception');
        $this->fs->file('config.xml', true);
        $this->loader->load(['config.xml']);
    }

}
