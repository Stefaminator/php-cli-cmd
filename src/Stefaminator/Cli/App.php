<?php


namespace Stefaminator\Cli;


abstract class App {

    public const EOL = "\n";

    /**
     * @return Cmd
     */
    abstract public function setup(): Cmd;

    public function eol(): void {
        echo self::EOL;
    }
}