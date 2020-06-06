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

        return Cmd::root()
            ->addOption('h|help', ['description' => 'Display the command help'])
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
                    ->setCallable(function(Cmd $cmd) {
                        $this->cmdList($cmd);
                    })
            )
            ->addSubCmd(
                Cmd::extend('show')
                    ->addSubCmd(
                        Cmd::extend('hello')
                            ->addOption('name:', [
                                'description' => 'The stats start date',
                                'isa' => 'string',
                                'required' => true
                            ])
                    )
                    ->addSubCmd(
                        Cmd::extend('stats')
                            ->addOption('start:', [
                                'description' => 'The stats start date',
                                'isa' => 'date',
                            ])
                    )
                    ->addSubCmd(
                        Cmd::extend('exception')
                            ->setCallable(static function(Cmd $cmd) {
                                throw new RuntimeException('fail');
                            })
                    )
            );
    }

    /** @noinspection PhpUnusedParameterInspection */
    public function cmd(Cmd $cmd): void {

        self::eol();

        echo "Main Command has been executed";

        self::eol();
    }

    /** @noinspection PhpUnusedParameterInspection */
    public function cmdList(Cmd $cmd): void {

        echo "\e[0;31;42mMerry Christmas!\e[0m\n";

        Color::green('Merry X-Mas');

        self::eol();

        Color::echo('Merry X-Mas', Color::FOREGROUND_COLOR_RED, Color::BACKGROUND_COLOR_GREEN);

        self::eol();

        Color::red('Merry X-Mas');

        self::eol();
    }

    /** @noinspection PhpUnusedParameterInspection */
    public function cmdShow(Cmd $cmd): void {

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

    public function cmdShowHello(Cmd $cmd): void {

        $name = $cmd->getProvidedOption('name');

        self::eol();

        echo "Hello $name";

        self::eol();
    }

    public function cmdShowStats(Cmd $cmd) {

        $date = $cmd->getProvidedOption('start');

        if($date !== null) {
            echo $date['year'] . '-' . $date['month'] . '-' . $date['day'];
        }

//        echo var_dump($date);
    }

    public function cmdHelp(Cmd $cmd) {

        $cmd->help();

    }

}