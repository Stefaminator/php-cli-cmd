#!/usr/bin/php
<?php

require __DIR__ . '/../vendor/autoload.php';

use Stefaminator\Cli\App;
use Stefaminator\Cli\CmdRunner;
use Stefaminator\Cli\Color;

(
    (new class extends App {

        public function setup(): CmdRunner {
            return $this->createRootCmd(
                new class extends CmdRunner {

                    public function init(): void {

                        $this
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
                            ]);

                        parent::init();
                    }

                    public function run(): void {

                        if ($this->hasProvidedOption('help')) {
                            $this->runHelp();
                            return;
                        }

                        $name = $this->getProvidedOption('name');

                        self::eol();
                        self::echo(sprintf('Hello %s', $name), Color::FOREGROUND_COLOR_YELLOW);
                        self::eol();
                        self::eol();

                        if ($this->hasProvidedOption('verbose')) {
                            $this->verbose();
                        }
                    }

                    public function help(): void {

                        echo '' .
                            ' This is the custom help for ' . Color::green('cli-app-options') . ' command. ' . self::EOL .
                            ' Please use the --name option to pass your name to this command and you will be greeted personally. ' . self::EOL .
                            ' ' . self::EOL .
                            ' ' . Color::green('php cli-app-options.php --name="great Stefaminator"') . self::EOL;
                    }

                    private function verbose(): void {

                        self::echo('--- VERBOSE OUTPUT ---', Color::FOREGROUND_COLOR_GREEN);
                        self::eol();
                        self::eol();

                        $this->outputProvidedOptions();

                        $this->outputProvidedArguments();
                    }

                    private function outputProvidedOptions(): void {

                        self::echo('  All current options...', Color::FOREGROUND_COLOR_GREEN);
                        self::eol();

                        $pOptions = $this->getAllProvidedOptions();
                        foreach ($pOptions as $k => $v) {
                            self::echo('    ' . $k . ': ' . json_encode($v), Color::FOREGROUND_COLOR_GREEN);
                            self::eol();
                        }
                        self::eol();
                    }

                    private function outputProvidedArguments(): void {

                        self::echo('  All current arguments...', Color::FOREGROUND_COLOR_GREEN);
                        self::eol();

                        $args = $this->arguments();
                        foreach ($args as $a) {
                            self::echo('    ' . $a, Color::FOREGROUND_COLOR_GREEN);
                            self::eol();
                        }
                        self::eol();
                    }
                }
            );
        }

    })
)->run();
