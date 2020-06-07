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

        return Cmd::root()
            ->addOption('h|help', ['description' => 'Display the command help'])
            ->setRunner(
                (new class extends CmdRunner {

                    public function run(): void {

                        self::eol();

                        echo "Main Command has been executed";

                        self::eol();
                    }
                })
            )
            ->addSubCmd(
                Cmd::extend('list')
                    ->addOption('xml', [
                        'description' => 'To output list as XML'
                    ])
                    ->addOption('f|format:', [
                        'description' => 'The output format (txt, xml, json, or md) [default: "txt"]',
                        'isa' => 'string',
                        'default' => 'txt'
                    ])
                    ->setRunner(
                        (new class extends CmdRunner {

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
            )
            ->addSubCmd(
                Cmd::extend('show')
                    ->setRunner(
                        (new class extends CmdRunner {

                            public function run(): void {

                                $total = 100;

                                self::eol();
                                self::eol();

                                for($i=0; $i<$total; $i++){

                                    Progress::showStatus($i+1, $total);

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
                    )
                    ->addSubCmd(
                        Cmd::extend('hello')
                            ->addOption('name:', [
                                'description' => 'The stats start date',
                                'isa' => 'string',
                                'required' => true
                            ])
                            ->setRunner(
                                (new class extends CmdRunner {

                                    public function run(): void {

                                        $cmd = $this->getCmd();

                                        $name = $cmd->getProvidedOption('name');

                                        self::eol();

                                        echo "Hello $name";

                                        self::eol();

                                    }
                                })
                            )
                    )
                    ->addSubCmd(
                        Cmd::extend('stats')
                            ->addOption('start:', [
                                'description' => 'The stats start date',
                                'isa' => 'date',
                            ])
                            ->setRunner(
                                (new class extends CmdRunner {

                                    public function run(): void {

                                        $cmd = $this->getCmd();

                                        $date = $cmd->getProvidedOption('start');

                                        if($date !== null) {
                                            echo $date['year'] . '-' . $date['month'] . '-' . $date['day'];
                                        }
                                    }
                                })
                            )
                    )
                    ->addSubCmd(
                        Cmd::extend('exception')
                            ->setRunner(
                                (new class extends CmdRunner {

                                    public function run(): void {
                                        throw new RuntimeException('fail');
                                    }
                                })
                            )
                    )
            )
            ->addSubCmd(
                Cmd::extend('greetings')
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
                    ])
                    ->setRunner(
                        (new class extends CmdRunner {

                            public function run(): void {

                                $cmd = $this->getCmd();

                                if($cmd->hasProvidedOption('help')) {
                                    $cmd->help();
                                    return;
                                }
                            }
                        })
                    )
            )
            ->addSubCmd(
                Cmd::extend('help')
                    ->setDescription('Displays help for this command.')
                    ->setRunner(
                        (new class extends CmdRunner {

                            public function run(): void {

                                $cmd = $this->getCmd();
                                $cmd->parent->help();

                            }
                        })
                    )
            );
    }


}