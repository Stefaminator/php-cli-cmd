# php-cli-cmd

**The PHP CLI CMD library. Helps you create well-structured PHP CLI apps quickly and easily.**


This library supports setting up options and arguments,  subcommands and offers an integrated command help. 
Use the color or progress bar helpers to visualize important outputs.

![Packagist PHP Version Support](https://img.shields.io/packagist/php-v/stefaminator/php-cli-cmd)
[![Latest Stable Version](https://poser.pugx.org/stefaminator/php-cli-cmd/v)](https://packagist.org/packages/stefaminator/php-cli-cmd)
[![Build Status](https://travis-ci.com/Stefaminator/php-cli-cmd.svg?token=sw1WsDwrxA6DdfoYeixr&branch=master)](https://travis-ci.com/Stefaminator/php-cli-cmd)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/Stefaminator/php-cli-cmd/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/Stefaminator/php-cli-cmd/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/Stefaminator/php-cli-cmd/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/Stefaminator/php-cli-cmd/?branch=master)
![Packagist License](https://img.shields.io/packagist/l/stefaminator/php-cli-cmd)

## Table of Contents

 - [Getting started](#getting-started)
 - [Build your first app](#build-your-first-app)
 - [Options](#options)
 - [Arguments](#arguments)

## Getting started

The simplest way of installing ist to refer to the sources on packagist.

    composer require stefaminator/php-cli-cmd

## Build your first app

Let's start by creating a new php file called `cli-app-helloworld.php` and require composers autoloader.

Create a custom class by extending `Stefaminator\Cli\App` and implement the required `setup` method 
which must return an instance of `Stefaminator\Cli\Cmd`. In the following example we created a root `Cmd` 
 and put the lines to execute for that root command into the `run` method. 

Finally, call the apps `run()` method and feel the magic.

(You can will this example here: [examples/cli-app-helloworld.php](./examples/cli-app-helloworld.php))

    <?php
    
    /** Please do not forget to require composers autoloader here */

    use Stefaminator\Cli\App;
    use Stefaminator\Cli\Cmd;
    
    (new class extends App {
    
        public function setup(): Cmd {    
            return (new class extends Cmd {
    
                public function init(): void {
                }
    
                public function run(): void {
                    echo "Hello world";
                    echo "\n";
                }
            });    
        }
    })->run();

If you execute that script from commandline you will see:

    # php cli-app-helloworld.php
    Hello World

Uhh, that was a lot of code for such a simple output, and you may think: "I can do 
the same with less code" - Yes, I believe you can!

## Options

**Ok, let's start with a simple example to add some options to our app!** 
Let's add one help flag option (`--help` or `-h`) to output the built in command help, 
and one name option to be able to say hello to a given name (i.e. `--name=Stefaminator`).


    <?php
    
    use Stefaminator\Cli\App;
    use Stefaminator\Cli\Cmd;
    use Stefaminator\Cli\Color;
    
    (new class extends App {
    
        public function setup(): Cmd {
            return (new class extends Cmd {
    
                public function init(): void {
    
                    $this
                        ->addOption('h|help', [
                            'description' => 'Displays the command help.'
                        ])
                        ->addOption('name:', [
                            'description' => 'Name option. This option requires a value.',
                            'isa' => 'string',
                            'default' => 'World'
                        ]);
                }
    
                public function run(): void {
    
                    if ($this->hasProvidedOption('help')) {
                        $this->runHelp();
                        return;
                    }
    
                    $name = $this->getProvidedOption('name');
    
                    self::eol();
                    self::echo(sprintf('Hello %s', $name), Color::FOREGROUND_COLOR_YELLOW);
                    self::eol();
                    self::eol();
                }
    
                public function help(): void {
    
                    echo '' .
                        ' This is the custom help for ' . Color::green('cli-app-options') . ' command. ' . self::EOL .
                        ' Please use the --name option to pass your name to this command and you will be greeted personally. ' . self::EOL .
                        ' ' . self::EOL .
                        ' ' . Color::green('php cli-app-options.php --name="great Stefaminator"') . self::EOL;
                }
            });
        }
    
    })->run();
    
### Adding options

Options should be added within the `Cmd::init()` method according to the following scheme:

    $this->addOption($specString, $configArray);

The `addOption()` method is chainable, so you may add options like this:

    $this
        ->addOption($specString1, $configArray1)
        ->addOption($specString2, $configArray2);
        
Please note that option parsing based on [https://github.com/c9s/GetOptionKit](https://github.com/c9s/GetOptionKit). 
Both the option specs and the option value validation based on that great library. So maybe check out their 
documentation first if you have any questions or issues with these features.
        
**Option specs**

Use the `$specString` (first argument of `addOption` method) to define the options 
long and/or short name and use the qualifiers `:`, `+` and `?` 
to determine if it should be a flag, required, multiple or optional value.

    h|help         flag option with single char option name (-h) and long option name (--help).
    n|name:        option require a value (-n=Stefaminator or --name="great Stefaminator").
    i|input+       option with multiple values (-i=file1 -i=file2).
    o|output?      option with optional value (--output or -o=output.txt)
    v              single character only option (-v)
    dir            long option name (--dir)

**Option config**

Specify more option attributes via `$configArray` (second argument of `addOption` method). Here is a list of possible keys:

    description    string    The description string is used in builtin command help
    isa            string    The option value type to validate the input (see: Option value validation)
    regex          string    A regex to validate the input value against (in case of isa=regex)
    default        mixed     The options default value
    incremental    bool      Typically used for verbose mode (with -vvv the value will be 3)
    

**Option value validation**

Possible values for the option configs `isa` key. 

    string         To ensure the given value is a string.
    number         To ensure the given value is a number.
    boolean        To ensure the given value is a boolean (true, false, 0, 1).
    file           To ensure the given value is an existing file.
    date           To ensure the given value is a valid date (Y-m-d).
    url            To ensure the given value is a valid url.
    email          To ensure the given value is a valid email.
    ip             To ensure the given value is a valid ip(v4/v6).
    ipv4           To ensure the given value is a valid ipv4.
    ipv6           To ensure the given value is a valid ipv6.
    regex          Validate the value against a custom regex (specified in regex key).
    
        

### Get provided options at runtime

The values of provided options may be catched within the `Cmd::run()` method using one of the following calls:

    /**
     * Returns true if a value for the given option name is present.
     * @param string $key Long option name if present, otherwise short option char
     * @return bool
     */
    $this->hasProvidedOption($key);
    
    /**
     * Returns the value for the given option name.
     * @param string $key Long option name if present, otherwise short option char
     * @return mixed
     */
    $value = $this->getProvidedOption($key);
    
    /**
     * Returns an array of key value pairs with all present option values.
     * @return array
     */
    $all = $this->getAllProvidedOptions();
        
    /**
     * Holds the original OptionResult of c9s/GetOptionKit library
     * @var OptionResult|null
     */
    $this->optionResult;
    
    
## Arguments

Arguments are generally always accepted and not validated by configuration. Any endpoint has
to decide if it processes arguments and must validate them by itself.
However, if you accept arguments you may want to make them visible and explained in help output.
Therefore, and only therefore, you may declare it like explained here.
    
### Adding arguments

Arguments should be added within the `Cmd::init()` method according to the following scheme:

    $this->addArgument($specString, $configArray);


The `addArgument()` method is chainable, so you may combine it with `addOption()` and add arguments like this:

    $this
        ->addOption($specString1, $configArray1)
        ->addOption($specString2, $configArray2)
        ->addArgument($specString3, $configArray3);

**Argument specs**

Use the `$specString` (first argument of `addArgument` method) to give the argument a meaningful name.

**Argument config**

Specify more argument attributes via `$configArray` (second argument of `addArgument` method). 
Here is a list of possible keys:

    description    string    The description string is used in builtin command help
    multiple       bool      The multiple flag is used in builtin command help to mark it as multiple (works only for the last declared arg)
    
    
### Get provided arguments at runtime

The values of provided options may be catched within the `Cmd::run()` method using one of the following calls:

    /**
     * Returns an indexed array of provided arguments.
     * @return array
     */
    $args = $this->arguments();
    
As explained above, it doesn't matter if the arguments had been declared via `addArgument()`. 
In contrast to options, which are only present after successful validation, arguments are present whenever they have been typed.
