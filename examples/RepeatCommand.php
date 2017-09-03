<?php namespace Tarsana\Command\Examples;

use Tarsana\Command\Command;

class RepeatCommand extends Command {

    protected function init ()
    {
        $this->name('Repeat')
             ->version('1.0.0')
             ->description('Repeats a word a number of times')
             ->syntax('word: string, count: (number: 3)')
             ->options(['--upper'])
             ->describe('word', 'The word to repeat')
             ->describe('count', 'The number of times to repeat the word')
             ->describe('--upper', 'Converts the result to uppercase');
    }

    protected function execute()
    {
        $result = str_repeat($this->args->word, $this->args->count);
        if ($this->option('--upper'))
            $result = strtoupper($result);
        $this->console->line($result);
    }

}
