<?php namespace Tarsana\Command\Examples\Tests;

use Tarsana\Command\Examples\ListCommand;
use Tarsana\Tester\CommandTestCase;


class ListCommandTest extends CommandTestCase {

    public function test_it_list_files_and_directories()
    {
        $this->havingFile('demo.txt', 'Some text here!')
             ->havingFile('doc.pdf')
             ->havingDir('src')
             ->command(new ListCommand)
             ->printsExactly('demo.txt<br>doc.pdf<br>src<br>');
    }

    public function test_it_prints_nothing_when_no_files()
    {
        $this->command(new ListCommand)
             ->printsExactly('');
    }

}
