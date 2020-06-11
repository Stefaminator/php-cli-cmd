#!/usr/bin/php
<?php

require __DIR__ . '/../vendor/autoload.php';

use Stefaminator\Cli\App;
use Stefaminator\Cli\CmdRunner;

(
    new class extends App {

        public function setup(): CmdRunner {

            return $this->createRootCmd(
                new class extends CmdRunner {
                    public function run(): void {
                        echo "Hello World";
                        echo "\n";
                    }
                }
            );

        }
    }
)->run();
