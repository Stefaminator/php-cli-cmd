#!/usr/bin/php
<?php

require __DIR__ . '/../vendor/autoload.php';

use Stefaminator\Cli\App;
use Stefaminator\Cli\AppParser;
use Stefaminator\Cli\Cmd;
use Stefaminator\Cli\CmdRunner;
use Stefaminator\Cli\Color;

AppParser::run(
    (new class extends App {

        public function setup(): Cmd {
            return Cmd::root()

                ->addOption('h|help', [
                    'description' => 'Displays the command help.'
                ])

                ->addOption('v|verbose', [
                    'description' => 'Flag to enable verbose output.'
                ])

                ->addOption('name:', [
                    'description' => 'Name option. This option requires a value.',
                    'isa' => 'string',
                    'default' => 'World'
                ])
                ->setRunner(
                    (new class extends CmdRunner {

                        public function run(): void {

                            $cmd = $this->getCmd();

                            if ($cmd->hasProvidedOption('help')) {
                                $cmd->help();
                                return;
                            }

                            $name = $cmd->getProvidedOption('name');

                            self::eol();
                            self::echo(sprintf('Hello %s', $name), Color::FOREGROUND_COLOR_YELLOW);
                            self::eol();
                            self::eol();

                            if ($cmd->hasProvidedOption('verbose')) {

                                self::echo('--- VERBOSE OUTPUT ---', Color::FOREGROUND_COLOR_GREEN);
                                self::eol();
                                self::eol();

                                self::echo('  All current options...', Color::FOREGROUND_COLOR_GREEN);
                                self::eol();

                                $pOptions = $cmd->getAllProvidedOptions();
                                foreach ($pOptions as $k => $v) {
                                    self::echo('    ' . $k . ': ' . $v, Color::FOREGROUND_COLOR_GREEN);
                                    self::eol();
                                }
                                self::eol();

                                self::echo('  All current arguments...', Color::FOREGROUND_COLOR_GREEN);
                                self::eol();

                                $args = $cmd->getAllProvidedArguments();
                                foreach ($args as $a) {
                                    self::echo('    ' . $a, Color::FOREGROUND_COLOR_GREEN);
                                    self::eol();
                                }
                                self::eol();

                            }

                        }
                    })
                );
        }

    })
);
