#!/usr/bin/php
<?php

require __DIR__ . '/../vendor/autoload.php';

use Stefaminator\Cli\App;
use Stefaminator\Cli\Cmd;

(new class extends App {

    public function setup(): Cmd {

        return (new class extends Cmd {

            public function init(): void {
            }

            public function run(): void {
                echo "Hello world";
                echo "\n";
            }
        });

    }
})->run();
