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
which must return an instance of `Stefaminator\Cli\Cmd`. In the following example a root `Cmd` object is
created and a static callable is assigned for that command. 

Finally pass your custom app class to `Stefaminator\Cli\AppParser::run()` and feel the magic.

    <?php
    
    /** Please do not forget to require composers autoloader here */
    
    use Stefaminator\Cli\App;
    use Stefaminator\Cli\AppParser;
    use Stefaminator\Cli\Cmd;
    
    AppParser::run(
        (new class extends App {    
        
            public function setup(): Cmd {
                return Cmd::root()
                    ->setCallable(static function (Cmd $cmd) {
                        echo 'Hello World' . self::EOL;
                    });
            }    
            
        })
    );

If you execute that script from commandline you will see:

    # php my-first-cli-app.php
    Hello World

Uhh, that was a lot of code for such a simple output and you may think: "I can do 
the same with less code" - Yes, I believe you can!

#### Ok, let's add some options

    <?php
    
    use Stefaminator\Cli\App;
    use Stefaminator\Cli\AppParser;
    use Stefaminator\Cli\Cmd;
    use Stefaminator\Cli\Color;
    
    AppParser::run(
        (new class extends App {
    
            public function setup(): Cmd {
                return Cmd::root()
                    ->addOption('v|verbose', [
                        'description' => 'Flag to enable verbose output'
                    ])
                    ->addOption('name:', [
                        'description' => 'Name option. This option requires a value.',
                        'isa' => 'string',
                        'default' => 'World'
                    ])
                    ->setCallable(static function (Cmd $cmd) {
    
                        $name = $cmd->optionResult->get('name');
    
                        self::eol();
                        self::echo(sprintf('Hello %s', $name), Color::FOREGROUND_COLOR_YELLOW);
                        self::eol();
    
                        if($cmd->optionResult->has('verbose')) {
                            $keys = array_keys($cmd->optionResult->keys);
                            self::eol();
                            self::echo('All current options...' . APP::EOL, Color::FOREGROUND_COLOR_GREEN);
                            foreach($keys as $k) {
                                self::echo('  ' . $k . ': ' . $cmd->optionResult->get($k) . APP::EOL, Color::FOREGROUND_COLOR_GREEN);
                            }
                            self::eol();
                        }
    
                    });
            }
    
        })
    );
