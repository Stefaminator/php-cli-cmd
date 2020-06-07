<?php


namespace Stefaminator\Cli;


abstract class App {
    /**
     * @return Cmd
     */
    abstract public function setup(): Cmd;

}