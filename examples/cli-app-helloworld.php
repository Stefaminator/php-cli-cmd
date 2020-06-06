#!/usr/bin/php
<?php

require __DIR__ . '/../vendor/autoload.php';

use Stefaminator\Cli\App;
use Stefaminator\Cli\AppParser;
use Stefaminator\Cli\Cmd;

AppParser::run(
    (new class extends App {

        public function setup(): Cmd {
            return Cmd::root()
                ->setCallable(static function(Cmd $cmd) {
                    echo 'Hello World' . self::EOL;
                });
        }

    })
);
