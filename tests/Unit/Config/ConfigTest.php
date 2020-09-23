<?php namespace Tarsana\Command\Tests\Unit\Config;

use PHPUnit\Framework\TestCase;
use Tarsana\Command\Config\Config;


class ConfigTest extends TestCase {

    public function test_it_gets_values() {
        $data = [
            'name' => 'Foo',
            'urls' => [
                'github' => 'some-link-here'
            ]
        ];

        $c = new Config($data);
        $this->assertEquals($data, $c->get());
        $this->assertEquals('Foo', $c->get('name'));
        $this->assertEquals('some-link-here', $c->get('urls.github'));
    }

    public function test_it_throws_exception() {
        $this->expectException('Exception');
        $data = [
            'name' => 'Foo',
            'urls' => [
                'github' => 'some-link-here'
            ]
        ];
        $c = new Config($data);
        $c->get('bar');
    }

}
