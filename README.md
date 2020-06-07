# php-cli-cmd

The PHP CLI CMD library. Helps you to create CLI Apps fast and easy. 

![Packagist PHP Version Support](https://img.shields.io/packagist/php-v/stefaminator/php-cli-cmd)
[![Latest Stable Version](https://poser.pugx.org/stefaminator/php-cli-cmd/v)](https://packagist.org/packages/stefaminator/php-cli-cmd)
[![Build Status](https://travis-ci.com/Stefaminator/php-cli-cmd.svg?token=sw1WsDwrxA6DdfoYeixr&branch=master)](https://travis-ci.com/Stefaminator/php-cli-cmd)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/Stefaminator/php-cli-cmd/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/Stefaminator/php-cli-cmd/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/Stefaminator/php-cli-cmd/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/Stefaminator/php-cli-cmd/?branch=master)
![Packagist License](https://img.shields.io/packagist/l/stefaminator/php-cli-cmd)

## Getting started

The simplest way of installing ist to refer to the sources on packagist.

    composer require stefaminator/php-cli-cmd

### Build your first App

Let's start by creating a new php file called `my-first-cli-app.php` and require composers autoloader.

Create a custom class by extending `Stefaminator\Cli\App` and implement the required `setup` method 
which must return an instance of `Stefaminator\Cli\Cmd`. In the following example we created a root `Cmd` 
 and assigned a `CmdRunner` for that root command. 

Finally, pass your custom app class to `Stefaminator\Cli\AppParser::run()` and feel the magic.

(You can will this example here: [examples/cli-app-helloworld.php](./examples/cli-app-helloworld.php))

    <?php
    
    /** Please do not forget to require composers autoloader here */

    use Stefaminator\Cli\App;
    use Stefaminator\Cli\AppParser;
    use Stefaminator\Cli\Cmd;
    use Stefaminator\Cli\CmdRunner;
    
    AppParser::run(
        (new class extends App {
    
            public function setup(): Cmd {
                return Cmd::root()
                    ->setRunner(
                        (new class extends CmdRunner {
                            public function run(): void {
                                echo "Hello World";
                                echo "\n";
                            }
                        })
                    );
            }
    
        })
    );

If you execute that script from commandline you will see:

    # php my-first-cli-app.php
    Hello World

Uhh, that was a lot of code for such a simple output and you may think: "I can do 
the same with less code" - Yes, I believe you can!

#### Options

**Ok, let's add some options!** One help flag option (`--help` or `-h`), one verbose flag option (`--verbose` or `-v`) 
and one name option that requires a value (`--name=Stefaminator`).

- Spec for **help** option: `h|help` 
- Spec for **verbose** option: `v|verbose` 
- Spec for **name** option: `name:`
 
When providing the help flag our cmd callback will output the builtin help. 
When providing the verbose flag it outputs all provided options and arguments.
The name option is used for output the `Hello %s` message.

    <?php
    
    use Stefaminator\Cli\App;
    use Stefaminator\Cli\AppParser;
    use Stefaminator\Cli\Cmd;
    use Stefaminator\Cli\Color;
    
    AppParser::run(
        (new class extends App {
    
            public function setup(): Cmd {
                return Cmd::root()
    
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
                    ])
                    
                    ->setRunner(
                        (new class extends CmdRunner {
    
                            public function run(): void {
    
                                $cmd = $this->getCmd();
    
                                if ($cmd->hasProvidedOption('help')) {
                                    $cmd->help();
                                    return;
                                }
    
                                $name = $cmd->getProvidedOption('name');
    
                                self::eol();
                                self::echo(sprintf('Hello %s', $name), Color::FOREGROUND_COLOR_YELLOW);
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
                                        self::echo('    ' . $k . ': ' . $v, Color::FOREGROUND_COLOR_GREEN);
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
                    );
            }
    
        })
    );