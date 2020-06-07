#!/usr/bin/php
<?php

require __DIR__ . '/../vendor/autoload.php';

use Stefaminator\Cli\App;
use Stefaminator\Cli\AppParser;
use Stefaminator\Cli\Cmd;
use Stefaminator\Cli\CmdRunner;
use Stefaminator\Cli\Color;

AppParser::run(
    new class extends App {

        public function setup(): Cmd {
            return Cmd::root()
                ->setRunner(
                    (new class extends CmdRunner {

                        public function run(): void {
                            $this->getCmd()->help();
                        }
                    })
                )
                ->addSubCmd(
                    Cmd::extend('show')
                        ->setDescription('This command is used to show something. Take a look at the subcommands.')
                        ->setRunner(
                            (new class extends CmdRunner {

                                public function run(): void {
                                    $this->getCmd()->help();
                                }
                            })
                        )
                        ->addSubCmd(
                            Cmd::extend('hello')
                                ->setDescription('Displays hello world.')
                                ->addOption('name:', [
                                    'description' => 'Name option. This option requires a value.',
                                    'isa' => 'string',
                                    'default' => 'World'
                                ])
                                ->setRunner(
                                    (new class extends CmdRunner {

                                        public function run(): void {

                                            $cmd = $this->getCmd();

                                            $name = $cmd->getProvidedOption('name');

                                            self::eol();
                                            self::echo(sprintf('Hello %s!', $name), Color::FOREGROUND_COLOR_CYAN);
                                            self::eol();
                                            self::eol();
                                        }
                                    })
                                )
                        )
                        ->addSubCmd(
                            Cmd::extend('phpversion')
                                ->setDescription('Displays the current php version of your cli.')
                                ->setRunner(
                                    (new class extends CmdRunner {

                                        public function run(): void {
                                            self::eol();
                                            self::echo('  Your PHP version is:', Color::FOREGROUND_COLOR_YELLOW);
                                            self::eol();
                                            self::echo('  ' . PHP_VERSION);
                                            self::eol();
                                            self::eol();
                                        }
                                    })
                                )
                        )
                );
        }

    }
);
