<?php


namespace Stefaminator\Cli\Test\Resources;

use Exception;
use RuntimeException;
use Stefaminator\Cli\App;
use Stefaminator\Cli\Cmd;
use Stefaminator\Cli\CmdRunner;
use Stefaminator\Cli\Color;
use Stefaminator\Cli\Progress;

class TestApp1 extends App {

    public function setup(): Cmd {

        return Cmd::createRootCmd(
            new class extends CmdRunner {

                public function init(Cmd $cmd): void {

                    $cmd
                        ->addOption('h|help', [
                            'description' => 'Display the command help'
                        ]);

                    parent::init($cmd);
                }

                public function run(): void {

                    self::eol();

                    echo "Main Command has been executed";

                    self::eol();
                }
            }
        )
            ->addSubCmd(
                Cmd::createSubCmd('list', new class extends CmdRunner {

                    public function init(Cmd $cmd): void {

                        $cmd
                            ->addOption('xml', [
                                'description' => 'To output list as XML'
                            ])
                            ->addOption('f|format:', [
                                'description' => 'The output format (txt, xml, json, or md) [default: "txt"]',
                                'isa' => 'string',
                                'default' => 'txt'
                            ]);

                        parent::init($cmd);
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
            ->addSubCmd(
                Cmd::createSubCmd('show', new class extends CmdRunner {

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
                    ->addSubCmd(
                        Cmd::createSubCmd('hello', new class extends CmdRunner {

                            public function init(Cmd $cmd): void {

                                $cmd
                                    ->addOption('h|help', ['description' => 'Display the command help'])
                                    ->addOption('name:', [
                                        'description' => 'The stats start date',
                                        'isa' => 'string',
                                        'default' => 'world',
                                        'required' => true
                                    ]);

                                parent::init($cmd);
                            }

                            public function run(): void {

                                $cmd = $this->getCmd();

                                if ($cmd->hasProvidedOption('help')) {
                                    $cmd->help();
                                    return;
                                }

                                $name = $cmd->getProvidedOption('name');

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
                        })
                    )
                    ->addSubCmd(
                        Cmd::createSubCmd('stats', new class extends CmdRunner {

                            public function init(Cmd $cmd): void {

                                $cmd
                                    ->addOption('v|verbose', [
                                        'description' => 'Flag to enable verbose output.'
                                    ])
                                    ->addOption('start:', [
                                        'description' => 'The stats start date',
                                        'isa' => 'date',
                                    ]);

                                parent::init($cmd);
                            }

                            public function run(): void {

                                $cmd = $this->getCmd();

                                $date = $cmd->getProvidedOption('start');

                                if ($date !== null) {
                                    $date_out = $date['year'] . '-' . $date['month'] . '-' . $date['day'];
                                    Color::echo($date_out, Color::FOREGROUND_COLOR_WHITE, Color::BACKGROUND_COLOR_PURPLE);
                                }
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
                                        self::echo('    ' . $k . ': ' . json_encode($v), Color::FOREGROUND_COLOR_GREEN);
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
                    )
                    ->addSubCmd(
                        Cmd::createSubCmd('exception', new class extends CmdRunner {

                            public function run(): void {
                                throw new RuntimeException('fail');
                            }
                        })
                    )
            )
            ->addSubCmd(
                Cmd::createSubCmd('greetings', new class extends CmdRunner {

                    public function init(Cmd $cmd): void {

                        $cmd
                            ->setDescription('Display some greetings.')
                            ->addOption('c|colored', [
                                'description' => 'Display the greetings colored.'
                            ])
                            ->addOption('h|help', [
                                'description' => 'Displays help for this command.'
                            ])
                            ->addArgument('event', [
                                'description' => 'The occasion of the greetings, may be xmas, easter, birthday or some custom event.'
                            ])
                            ->addArgument('names', [
                                'description' => 'Multiple names of the greetings receivers.',
                                'multiple' => true
                            ]);

                        parent::init($cmd);
                    }

                    public function run(): void {

                        $cmd = $this->getCmd();

                        if ($cmd->hasProvidedOption('help')) {
                            $cmd->help();
                            return;
                        }
                    }
                })
            )
            ->addSubCmd(
                Cmd::createSubCmd('help', new class extends CmdRunner {

                    public function init(Cmd $cmd): void {

                        $cmd
                            ->setDescription('Displays help for this command.');

                        parent::init($cmd);
                    }

                    public function run(): void {

                        $cmd = $this->getCmd();
                        $cmd->parent->help();

                    }
                })
            );
    }

}