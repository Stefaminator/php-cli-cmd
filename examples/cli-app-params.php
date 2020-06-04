#!/usr/bin/php
<?php

require __DIR__ . '/../vendor/autoload.php';

use Stefaminator\Cli\App;
use Stefaminator\Cli\AppParser;
use Stefaminator\Cli\Cmd;
use Stefaminator\Cli\Color;

AppParser::run(
    (new class extends App {

        public function setup(): Cmd {
            return Cmd::root()
                ->addParam('v|verbose', [
                    'description' => 'Flag to enable verbose output'
                ])
                ->addParam('n|name:', [
                    'description' => 'Name parameter. This param requires a value.',
                    'isa' => 'string',
                    'default' => 'World'
                ])
                ->setCallable(static function (Cmd $cmd) {

                    $name = $cmd->options->get('name');

                    self::eol();
                    self::echo(sprintf('Hello %s', $name), Color::FOREGROUND_COLOR_YELLOW);
                    self::eol();

                    if($cmd->options->has('verbose')) {
                        $keys = array_keys($cmd->options->keys);
                        self::eol();
                        self::echo('All option params...', Color::FOREGROUND_COLOR_GREEN);
                        foreach($keys as $k) {
                            self::echo(self::PADDING . $k . ': ' . $cmd->options->get($k), Color::FOREGROUND_COLOR_GREEN);
                        }
                        self::eol();
                    }

                });
        }

    })
);
