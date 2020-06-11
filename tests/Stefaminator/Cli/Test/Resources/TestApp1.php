<?php


namespace Stefaminator\Cli\Test\Resources;

use Exception;
use RuntimeException;
use Stefaminator\Cli\App;
use Stefaminator\Cli\Cmd;
use Stefaminator\Cli\Color;
use Stefaminator\Cli\Progress;

class TestApp1 extends App {

    public function setup(): Cmd {

        return (new class extends Cmd {

            public function init(): void {

                $this
                    ->addOption('h|help', [
                        'description' => 'Display the command help'
                    ]);
            }

            public function run(): void {

                self::eol();

                echo "Main Command has been executed";

                self::eol();
            }
        })
            ->addChild((new class('list') extends Cmd {

                public function init(): void {

                    $this
                        ->addOption('xml', [
                            'description' => 'To output list as XML'
                        ])
                        ->addOption('f|format:', [
                            'description' => 'The output format (txt, xml, json, or md) [default: "txt"]',
                            'isa' => 'string',
                            'default' => 'txt'
                        ]);
                }

                public function run(): void {

                    echo "\e[0;31;42mMerry Christmas!\e[0m\n";

                    echo Color::green('Merry X-Mas');

                    self::eol();

                    Color::echo('Merry X-Mas', Color::FOREGROUND_COLOR_RED, Color::BACKGROUND_COLOR_GREEN);

                    self::eol();

                    echo Color::red('Merry X-Mas');

                    self::eol();
                }
            })
            )
            ->addChild((new class('show') extends Cmd {

                public function init(): void {
                }

                public function run(): void {

                    $total = 100;

                    self::eol();
                    self::eol();

                    for ($i = 0; $i < $total; $i++) {

                        Progress::showStatus($i + 1, $total);

                        try {
                            $micro_seconds = random_int(1000, 100000);
                        } catch (Exception $e) {
                            $micro_seconds = 10000;
                        }

                        usleep($micro_seconds);
                    }

                    self::eol();
                    self::eol();

                }
            })
                ->addChild((new class('hello') extends Cmd {

                    public function init(): void {

                        $this
                            ->addOption('h|help', ['description' => 'Display the command help'])
                            ->addOption('name:', [
                                'description' => 'The stats start date',
                                'isa' => 'string',
                                'default' => 'world',
                                'required' => true
                            ]);
                    }

                    public function run(): void {

                        if ($this->hasProvidedOption('help')) {
                            $this->runHelp();
                            return;
                        }

                        $name = $this->getProvidedOption('name');

                        self::eol();

                        echo "Hello $name";

                        self::eol();

                    }

                    public function help(): void {

                        echo '' .
                            Color::red('test') . ' ' .
                            Color::green('test') . ' ' .
                            Color::blue('test') . ' ' .
                            Color::yellow('test') . ' ' .
                            Color::purple('test');

                    }
                }))
                ->addChild((new class('stats') extends Cmd {

                    public function init(): void {

                        $this
                            ->addOption('v|verbose', [
                                'description' => 'Flag to enable verbose output.'
                            ])
                            ->addOption('start:', [
                                'description' => 'The stats start date',
                                'isa' => 'date',
                            ]);
                    }

                    public function run(): void {

                        $date = $this->getProvidedOption('start');

                        if ($date !== null) {
                            $date_out = $date['year'] . '-' . $date['month'] . '-' . $date['day'];
                            Color::echo($date_out, Color::FOREGROUND_COLOR_WHITE, Color::BACKGROUND_COLOR_PURPLE);
                        }
                        self::eol();
                        self::eol();


                        if ($this->hasProvidedOption('verbose')) {

                            self::echo('--- VERBOSE OUTPUT ---', Color::FOREGROUND_COLOR_GREEN);
                            self::eol();
                            self::eol();

                            self::echo('  All current options...', Color::FOREGROUND_COLOR_GREEN);
                            self::eol();

                            $pOptions = $this->getAllProvidedOptions();
                            foreach ($pOptions as $k => $v) {
                                self::echo('    ' . $k . ': ' . json_encode($v), Color::FOREGROUND_COLOR_GREEN);
                                self::eol();
                            }
                            self::eol();

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
                }))
                ->addChild(
                    (new class('exception') extends Cmd {

                        public function init(): void {
                        }

                        public function run(): void {
                            throw new RuntimeException('fail');
                        }
                    })
                )
            )
            ->addChild((new class('greetings') extends Cmd {

                public function init(): void {

                    $this
                        ->setDescription('Display some greetings.')
                        ->addArgument('event', [
                            'description' => 'The occasion of the greetings, may be xmas, easter, birthday or some custom event.'
                        ])
                        ->addArgument('names', [
                            'description' => 'Multiple names of the greetings receivers.',
                            'multiple' => true
                        ])
                        ->addOption('c|colored', [
                            'description' => 'Display the greetings colored.'
                        ])
                        ->addOption('h|help', [
                            'description' => 'Displays help for this command.'
                        ]);
                }

                public function run(): void {

                    if ($this->hasProvidedOption('help')) {
                        $this->runHelp();
                        return;
                    }
                }
            }))
            ->addChild((new class('help') extends Cmd {

                public function init(): void {

                    $this
                        ->setDescription('Displays help for this command.');
                }

                public function run(): void {

                    $this->parent->runHelp();
                }
            }));
    }

}