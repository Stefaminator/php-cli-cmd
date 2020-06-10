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
            return Cmd::createRootCmd(
                new class extends CmdRunner {

                    public function run(): void {
                        $this->runHelp();
                    }
                }
            )
                ->addSubCmd(
                    Cmd::createSubCmd('show', new class extends CmdRunner {

                        public function init(Cmd $cmd): void {

                            $this->description = 'This command is used to show something. Take a look at the subcommands.';

                            parent::init($cmd);
                        }

                        public function run(): void {
                            $this->runHelp();
                        }

                    })
                        ->addSubCmd(
                            Cmd::createSubCmd('hello', new class extends CmdRunner {

                                public function init(Cmd $cmd): void {

                                    $this->description = 'Displays hello world.';

                                    $this
                                        ->addOption('name:', [
                                            'description' => 'Name option. This option requires a value.',
                                            'isa' => 'string',
                                            'default' => 'World'
                                        ]);

                                    parent::init($cmd);
                                }

                                public function run(): void {

                                    $name = $this->getProvidedOption('name');

                                    self::eol();
                                    self::echo(sprintf('Hello %s!', $name), Color::FOREGROUND_COLOR_CYAN);
                                    self::eol();
                                    self::eol();
                                }
                            })
                        )
                        ->addSubCmd(
                            Cmd::createSubCmd('phpversion', new class extends CmdRunner {

                                public function init(Cmd $cmd): void {

                                    $this->description = 'Displays the current php version of your cli.';

                                    parent::init($cmd);
                                }

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
                );
        }

    }
);
