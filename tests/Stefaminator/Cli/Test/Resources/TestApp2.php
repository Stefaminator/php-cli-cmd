<?php


namespace Stefaminator\Cli\Test\Resources;


use Stefaminator\Cli\App;
use Stefaminator\Cli\Cmd;
use Stefaminator\Cli\CmdRunner;

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
            ->setRunner(
                (new class extends CmdRunner {

                    public function run(): void {
                    }
                })
            );
    }
}