<?php

use Tarsana\Syntax\Factory as S;
use Tarsana\Syntax\ObjectSyntax;
use Tarsana\Command\Syntax\SyntaxBuilder;

class SyntaxBuilderTest extends \Codeception\Test\Unit
{
    protected $tester;

    public function test_create_from_string() {
        $testCases = [
            '' => '{ ,}',
            'name' => '{ ,name}',
            'name [#age]' => '{ ,name,[#age]}'
        ];
        foreach ($testCases as $in => $out) {
            $syntax = SyntaxBuilder::of($in)->get();
            $this->assertEquals($out, S::syntax()->dump($syntax));
        }
    }

    public function test_create_from_syntax() {
        $syntax = S::fromString('{ ,name,[#age]}');
        $this->assertEquals('{ ,name,[#age]}', S::syntax()->dump(SyntaxBuilder::of($syntax)->get()));
    }

    public function test_describes_arguments() {
        $syntax = SyntaxBuilder::of('name #stars #forks owner{name,email,followers{name,email}[]}')
            ->describe('name', 'The name of the repository')
            ->describe('stars', 'Number of stars of the repository')
            ->describe('forks', 'Number of forks of the repository')
            ->describe('owner', 'The owner of the repository')
            ->describe('owner.name', 'The name of the owner')
            ->describe('owner.email', 'The email of the owner')
            ->describe('owner.followers', 'The followers of the owner')
            ->describe('owner.followers.name', 'The name of the follower')
            ->describe('owner.followers.email', 'The email of the follower')
            ->get();

        $this->assertTrue($syntax instanceof ObjectSyntax);
        $this->assertEquals('The name of the repository', $syntax->field('name')->description());
        $this->assertEquals('Number of stars of the repository', $syntax->field('stars')->description());
        $this->assertEquals('Number of forks of the repository', $syntax->field('forks')->description());
        $this->assertEquals('The owner of the repository', $syntax->field('owner')->description());
        $this->assertEquals('The name of the owner', $syntax->field('owner')->field('name')->description());
        $this->assertEquals('The email of the owner', $syntax->field('owner')->field('email')->description());
        $this->assertEquals('The followers of the owner', $syntax->field('owner')->field('followers')->description());
        $this->assertEquals('The name of the follower', $syntax->field('owner')->field('followers')->itemSyntax()->field('name')->description());
        $this->assertEquals('The email of the follower', $syntax->field('owner')->field('followers')->itemSyntax()->field('email')->description());
    }

}
