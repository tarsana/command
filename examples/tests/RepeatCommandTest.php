<?php namespace Tarsana\Command\Examples\Tests;

use Tarsana\Command\Examples\RepeatCommand;
use Tarsana\Tester\CommandTestCase;


class RepeatCommandTest extends CommandTestCase {

    public function test_it_repeats_word_three_times()
    {
        $this->command(new RepeatCommand, ['foo'])
             ->argsEqual((object) [
                'word' => 'foo',
                'count' => 3
             ])
             ->optionsEqual([
                '--upper' => false
             ])
             ->printsExactly("foofoofoo<br>");
    }

    public function test_it_repeats_word_n_times()
    {
        $this->command(new RepeatCommand, ['bar', '5'])
             ->argsEqual((object) [
                'word' => 'bar',
                'count' => 5
             ])
             ->optionsEqual([
                '--upper' => false
             ])
             ->printsExactly("barbarbarbarbar<br>");
    }

    public function test_it_repeats_word_n_times_uppercase()
    {
        $this->command(new RepeatCommand, ['bar', '5', '--upper'])
             ->argsEqual((object) [
               'word' => 'bar',
               'count' => 5
             ])
             ->optionsEqual([
               '--upper' => true
             ])
             ->printsExactly("BARBARBARBARBAR<br>");
    }

}
