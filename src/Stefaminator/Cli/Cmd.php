<?php

namespace Stefaminator\Cli;

use Exception;
use GetOptionKit\OptionCollection;
use GetOptionKit\OptionResult;
use ReflectionFunction;
use RuntimeException;


class Cmd {

    /**
     * @var Cmd
     */
    public $parent;

    /**
     * @var string
     */
    public $cmd;

    /**
     * @var string
     */
    public $descr;

    /**
     * @var array
     */
    public $optionSpecs;

    /**
     * @var OptionCollection
     */
    private $optionCollection;

    /**
     * @var OptionResult|null
     */
    public $optionResult;

    /**
     * @var Exception
     */
    public $optionParseException;

    /**
     * @var string[]
     */
    public $arguments = [];

    /**
     * @var Cmd[]
     */
    public $subcommands = [];

    /**
     * @var callable|null
     */
    private $callable;


    public function __construct(string $cmd) {
        $this->cmd = $cmd;
        if ($cmd !== 'help') {
            $this->addSubCmd(
                self::extend('help')
                    ->setDescription('Displays help for this command.')
                    ->setCallable(static function (Cmd $cmd) {
                        $cmd->parent->help();
                    })
            );
        }
    }

    public function addOption(string $specString, array $config): self {

        $this->optionSpecs[$specString] = $config;

        return $this;
    }

    public function addSubCmd(Cmd $cmd): self {

        $cmd->parent = $this;
        $this->subcommands[$cmd->cmd] = $cmd;

        return $this;
    }

    public function setDescription(string $descr): self {

        $this->descr = $descr;

        return $this;
    }

    /**
     * @param callable $callable
     * @return $this
     */
    public function setCallable(callable $callable): self {

        try {
            $this->callable = $this->validateCallable($callable);

        } catch (Exception $e) {
            echo __METHOD__ . ' has been called with invalid callable: ' . $e->getMessage() . "\n";
        }


        return $this;
    }

    public function hasSubCmd(string $cmd): bool {
        return array_key_exists($cmd, $this->subcommands);
    }

    public function hasProvidedOption(string $key): bool {
        return $this->optionResult !== null && $this->optionResult->has($key);
    }

    public function getProvidedOption(string $key) {
        if ($this->optionResult !== null) {
            return $this->optionResult->get($key);
        }
        return null;
    }

    public function getSubCmd(string $cmd): ?Cmd {
        if ($this->hasSubCmd($cmd)) {
            return $this->subcommands[$cmd];
        }
        return null;
    }

    public function getMethodName(): string {
        $cmd = $this;
        $pwd = [];

        while ($cmd !== null) {
            $pwd[] = $cmd->parent !== null ? $cmd->cmd : 'cmd';
            $cmd = $cmd->parent;
        }

        $pwd = array_reverse($pwd);

        $pwd_str = '';
        foreach ($pwd as $p) {
            $pwd_str .= ucfirst(strtolower($p));
        }

        return lcfirst($pwd_str);
    }

    public function getCallable(): ?callable {
        return $this->callable;
    }

    public function getOptionCollection(): OptionCollection {

        if ($this->optionCollection !== null) {
            return $this->optionCollection;
        }

        $specs = (array)$this->optionSpecs;

        $collection = new OptionCollection();

        foreach ($specs as $k => $v) {
            $opt = $collection->add($k, $v['description']);
            if (array_key_exists('isa', $v)) {
                $opt->isa($v['isa']);
            }
            if (array_key_exists('default', $v)) {
                $opt->defaultValue($v['default']);
            }
        }

        $this->optionCollection = $collection;
        return $this->optionCollection;
    }

    public function handleOptionParseException(): bool {

        if ($this->optionParseException === null) {
            return false;
        }

        App::eol();
        App::echo('Uups, something went wrong!', Color::FOREGROUND_COLOR_RED);
        App::eol();
        App::echo($this->optionParseException->getMessage(), Color::FOREGROUND_COLOR_RED);
        App::eol();

        $this->help();

        return true;
    }

    public function help(): void {
        (new HelpRunner($this))->run();
    }


    /**
     * @param callable $callable
     * @return callable
     * @throws Exception
     */
    private function validateCallable(callable $callable): callable {

        $check = new ReflectionFunction($callable);
        $parameters = $check->getParameters();

        if (count($parameters) !== 1) {
            throw new RuntimeException('Invalid number of Parameters. Should be 1.');
        }

        $type = $parameters[0]->getType();

        if ($type === null) {
            throw new RuntimeException('Named type of Parameter 1 should be "' . __CLASS__ . '".');
        }

        /** @noinspection PhpPossiblePolymorphicInvocationInspection */
        $tname = $type->getName();

        if ($tname !== __CLASS__) {
            throw new RuntimeException('Named type of Parameter 1 should be "' . __CLASS__ . '".');
        }

        return $callable;
    }

    public static function extend(string $cmd): Cmd {
        return new class($cmd) extends Cmd {
        };
    }

    public static function root(): Cmd {
        return self::extend('__root');
    }
}