<?php


namespace Stefaminator\Cli;


abstract class CmdRunner {

    /**
     * @var Cmd
     */
    private $cmd;

    /**
     * CmdRunner constructor.
     * @param Cmd $cmd
     */
    public function __construct(Cmd $cmd) {
        $this->cmd = $cmd;
    }

    /**
     * @return Cmd
     */
    public function cmd(): Cmd {
        return $this->cmd;
    }

    /**
     * Run the cmd
     */
    abstract public function run(): void;
}