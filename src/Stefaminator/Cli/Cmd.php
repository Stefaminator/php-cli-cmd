<?php

namespace Stefaminator\Cli;


class Cmd {

    /**
     * @var Cmd
     */
    public $parent;

    /**
     * @var string
     */
    public $cmd;

    /**
     * @var Cmd[]
     */
    public $subcommands = [];

    /**
     * @var CmdRunner
     */
    private $runner;


    public function __construct(string $cmd, CmdRunner $runner) {
        $this->cmd = $cmd;

        $runner->init($this);
        $this->runner = $runner;
    }

    public function addSubCmd(Cmd $cmd): self {

        $cmd->parent = $this;
        $this->subcommands[$cmd->cmd] = $cmd;

        return $this;
    }

    public function hasSubCmd(string $cmd): bool {
        return array_key_exists($cmd, $this->subcommands);
    }

    public function getSubCmd(string $cmd): ?Cmd {
        if ($this->hasSubCmd($cmd)) {
            return $this->subcommands[$cmd];
        }
        return null;
    }

    public function runner(): CmdRunner {
        return $this->runner;
    }

    public static function createRootCmd(CmdRunner $runner): Cmd {
        return new Cmd('__root', $runner);
    }

    public static function createSubCmd(string $cmd, CmdRunner $runner): Cmd {
        return new Cmd($cmd, $runner);
    }

}