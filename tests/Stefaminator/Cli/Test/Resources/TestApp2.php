<?php


namespace Stefaminator\Cli\Test\Resources;


use Stefaminator\Cli\App;
use Stefaminator\Cli\CmdRunner;

class TestApp2 extends App {

    public function setup(): CmdRunner {

        return $this->createRootCmd(
            new class extends CmdRunner {

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

                    parent::init();
                }

                public function run(): void {
                }
            }
        );
    }
}