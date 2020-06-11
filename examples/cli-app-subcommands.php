#!/usr/bin/php
<?php

require __DIR__ . '/../vendor/autoload.php';

use Stefaminator\Cli\App;
use Stefaminator\Cli\Cmd;
use Stefaminator\Cli\Color;

(new class extends App {

    public function setup(): Cmd {
        return (
        new class extends Cmd {

            public function init(): void {
            }

            public function run(): void {
                $this->runHelp();
            }
        })
            ->addChild((new class('show') extends Cmd {

                public function init(): void {

                    $this
                        ->setDescription(
                            'This command is used to show something. Take a look at the subcommands.'
                        );

                }

                public function run(): void {
                    $this->runHelp();
                }

            })
                ->addChild((new class('hello') extends Cmd {

                    public function init(): void {

                        $this
                            ->setDescription(
                                'Displays hello world.'
                            )
                            ->addOption('name:', [
                                'description' => 'Name option. This option requires a value.',
                                'isa' => 'string',
                                'default' => 'World'
                            ]);
                    }

                    public function run(): void {

                        $name = $this->getProvidedOption('name');

                        self::eol();
                        self::echo(sprintf('Hello %s!', $name), Color::FOREGROUND_COLOR_CYAN);
                        self::eol();
                        self::eol();
                    }
                }))
                ->addChild((new class('phpversion') extends Cmd {

                    public function init(): void {

                        $this
                            ->setDescription(
                                'Displays the current php version of your cli.'
                            );
                    }

                    public function run(): void {
                        self::eol();
                        self::echo('  Your PHP version is:', Color::FOREGROUND_COLOR_YELLOW);
                        self::eol();
                        self::echo('  ' . PHP_VERSION);
                        self::eol();
                        self::eol();
                    }
                }))
            );
    }

})->run();
