<?php namespace Tarsana\Command;

use Tarsana\Command\Command;

/**
 * A SubCommand which has a parent command and can access it.
 */
class SubCommand extends Command {

    /**
     * The parent command.
     *
     * @var Tarsana\Command\Command
     */
    protected $parent;

    /**
     * Creates a new SubCommand.
     *
     * @param Command $parent
     */
    public function __construct(Command $parent)
    {
        parent::__construct();
        $this->parent = $parent;
        $this->console = $parent->console;
    }

    protected function setupSubCommands()
    {
        return $this;
    }

    /**
     * parent getter and setter.
     *
     * @param  Tarsana\Command\Command|null
     * @return Tarsana\Command\Command
     */
    public function parent(Command $value = null)
    {
        if (null === $value) {
            return $this->parent;
        }
        $this->parent = $value;
        return $this;
    }

}
