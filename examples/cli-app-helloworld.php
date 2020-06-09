#!/usr/bin/php
<?php

require __DIR__ . '/../vendor/autoload.php';

use Stefaminator\Cli\App;
use Stefaminator\Cli\AppParser;
use Stefaminator\Cli\Cmd;
use Stefaminator\Cli\CmdRunner;

AppParser::run(
    new class extends App {

        public function setup(): Cmd {

            return Cmd::createRootCmd(
                new class extends CmdRunner {
                    public function run(): void {
                        echo "Hello World";
                        echo "\n";
                    }
                }
            );

        }
    }
);
