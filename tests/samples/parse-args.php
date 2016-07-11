<?php
require __DIR__.'/../../vendor/autoload.php';

use Tarsana\Command\Command;


class PersonCommand extends Command {

    protected function init()
    {
        $this->syntax('name [#age] friends{name,[#age]}[]');
    }

    public function execute()
    {
        $this->console->out(json_encode($this->args));
    }

}

(new PersonCommand)->run();
