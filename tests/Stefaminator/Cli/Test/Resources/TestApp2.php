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
                            'description' => 'Flag to enable verbose output',
                            'incremental' => true
                        ])
                        ->addOption('name:', [
                            'description' => 'Name option. This option requires a value.',
                            'isa' => 'string',
                            'default' => 'World'
                        ])
                        ->addOption('age:', [
                            'description' => 'Tell us your current age, please!',
                            'isa' => 'regex',
                            'regex' => '/^[0-9]+$/'
                        ])
                        ->addOption('!', [
                            'description' => 'Invalid option specs!'
                        ])
                        ->addArgument('arg1', [
                            'multiple' => true
                        ])
                        ->addArgument('arg2', [
                            'multiple' => true
                        ]);
                }

                public function run(): void {
                }
            }
        );
    }
}