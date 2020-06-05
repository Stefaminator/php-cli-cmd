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
                ->addOption('v|verbose', [
                    'description' => 'Flag to enable verbose output'
                ])
                ->addOption('name:', [
                    'description' => 'Name option. This option requires a value.',
                    'isa' => 'string',
                    'default' => 'World'
                ])
                ->setCallable(static function (Cmd $cmd) {

                    $name = $cmd->optionResult->get('name');

                    self::eol();
                    self::echo(sprintf('Hello %s', $name), Color::FOREGROUND_COLOR_YELLOW);
                    self::eol();

                    if($cmd->optionResult->has('verbose')) {
                        $keys = array_keys($cmd->optionResult->keys);
                        self::eol();
                        self::echo('--- VERBOSE OUTPUT ---' . APP::EOL, Color::FOREGROUND_COLOR_GREEN);
                        self::eol();
                        self::echo('  All current options...' . APP::EOL, Color::FOREGROUND_COLOR_GREEN);
                        foreach($keys as $k) {
                            self::echo('    ' . $k . ': ' . $cmd->optionResult->get($k) . APP::EOL, Color::FOREGROUND_COLOR_GREEN);
                        }
                        self::eol();

                        self::echo('  All current arguments...' . APP::EOL, Color::FOREGROUND_COLOR_GREEN);
                        $args = $cmd->arguments;
                        foreach($args as $a) {
                            self::echo('    ' . $a . APP::EOL, Color::FOREGROUND_COLOR_GREEN);
                        }

                    }

                });
        }

    })
);
