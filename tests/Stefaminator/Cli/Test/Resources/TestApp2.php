<?php


namespace Stefaminator\Cli\Test\Resources;


use Stefaminator\Cli\App;
use Stefaminator\Cli\Cmd;

class TestApp2 extends App {

    public function setup(): Cmd {

        return (new class extends Cmd {

                public function init(): void {

                    $this
                        ->addOption('h|help', [
                            'description' => 'Display the command help'
                        ])
                        ->addOption('v|verbose', [
                            'description' => 'Flag to enable verbose output'
                        ])
                        ->addOption('name:', [
                            'description' => 'Name option. This option requires a value.',
                            'isa' => 'string',
                            'default' => 'World'
                        ]);
                }

                public function run(): void {
                }
            }
        );
    }
}