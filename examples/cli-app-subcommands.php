#!/usr/bin/php
<?php

require __DIR__ . '/../vendor/autoload.php';

use Stefaminator\Cli\App;
use Stefaminator\Cli\CmdRunner;
use Stefaminator\Cli\Color;

(
new class extends App {

    public function setup(): CmdRunner {
        return $this->createRootCmd(new class extends CmdRunner {

            public function run(): void {
                $this->runHelp();
            }
        })
            ->addChildNode(
                $this->createSubCmd('show', new class extends CmdRunner {

                    public function init(): void {

                        $this->description = 'This command is used to show something. Take a look at the subcommands.';

                        parent::init();
                    }

                    public function run(): void {
                        $this->runHelp();
                    }

                })
                    ->addChildNode(
                        $this->createSubCmd('hello', new class extends CmdRunner {

                            public function init(): void {

                                $this->description = 'Displays hello world.';

                                $this
                                    ->addOption('name:', [
                                        'description' => 'Name option. This option requires a value.',
                                        'isa' => 'string',
                                        'default' => 'World'
                                    ]);

                                parent::init();
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
                    ->addChildNode(
                        $this->createSubCmd('phpversion', new class extends CmdRunner {

                            public function init(): void {

                                $this->description = 'Displays the current php version of your cli.';

                                parent::init();
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
)->run();
