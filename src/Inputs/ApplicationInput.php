<?php namespace Tarsana\Application\Inputs;


class ApplicationInput {

    /**
     * array of provided flags to the application.
     *
     * @var array
     */
    protected $flags;

    /**
     * The command name.
     *
     * @var string
     */
    protected $command;

    /**
     * The command arguments as string.
     *
     * @var string
     */
    protected $args;

    /**
     * Creates a new ApplicationInput.
     *
     * @param array $flags
     * @param string $command
     * @param string $args
     */
    public function __construct ($flags, $command, $args) {
        $this->flags = $flags;
        $this->command = $command;
        $this->args = $args;
    }

    /**
     * flags getter.
     *
     * @return array
     */
    public function flags ()
    {
        return $this->flags;
    }

    /**
     * command getter.
     *
     * @return string
     */
    public function command ()
    {
        return $this->command;
    }

    /**
     * args getter.
     *
     * @return string
     */
    public function args ()
    {
        return $this->args;
    }
}
