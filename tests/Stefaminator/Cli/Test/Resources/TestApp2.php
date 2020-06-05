<?php


namespace Stefaminator\Cli\Test\Resources;


use Stefaminator\Cli\App;
use Stefaminator\Cli\Cmd;

class TestApp2 extends App {

    public function setup(): Cmd {

        return Cmd::root()
            ->addOption('h|help', ['description' => 'Display the command help'])

            ->addOption('v|verbose', [
                'description' => 'Flag to enable verbose output'
            ])
            ->addOption('name:', [
                'description' => 'Name option. This option requires a value.',
                'isa' => 'string',
                'default' => 'World'
            ])
            ->setCallable(static function (Cmd $cmd) {
            });
    }
}