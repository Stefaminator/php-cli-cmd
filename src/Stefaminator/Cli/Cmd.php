<?php

namespace Stefaminator\Cli;

use Exception;
use GetOptionKit\OptionCollection;
use GetOptionKit\OptionResult;


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
    public $optionSpecs = [];

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
     * @var array
     */
    public $argumentSpecs = [];

    /**
     * @var string[]
     */
    public $arguments = [];

    /**
     * @var Cmd[]
     */
    public $subcommands = [];

    /**
     * @var CmdRunner|null
     */
    private $runner;


    public function __construct(string $cmd) {
        $this->cmd = $cmd;
    }

    public function addOption(string $specString, array $config): self {

        $this->optionSpecs[$specString] = $config;

        return $this;
    }

    public function addArgument(string $specString, array $config): self {

        foreach ($this->argumentSpecs as $k => $v) {
            if (array_key_exists('multiple', $v)) {
                unset($this->argumentSpecs[$k]['multiple']);
            }
        }

        $config['index'] = count($this->argumentSpecs);

        $this->argumentSpecs[$specString] = $config;

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

    public function setRunner(CmdRunner $runner): self {

        $runner->setCmd($this);

        $this->runner = $runner;

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

    public function getAllProvidedOptions(): array {
        $r = [];
        if ($this->optionResult !== null) {
            $keys = array_keys($this->optionResult->keys);
            foreach($keys as $key) {
                $r[$key] = $this->getProvidedOption($key);
            }
        }
        return $r;
    }

    public function getAllProvidedArguments(): array {
        return $this->arguments;
    }

    public function getSubCmd(string $cmd): ?Cmd {
        if ($this->hasSubCmd($cmd)) {
            return $this->subcommands[$cmd];
        }
        return null;
    }

    public function getRunner(): ?CmdRunner {
        return $this->runner;
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

        CmdRunner::eol();
        CmdRunner::echo('Uups, something went wrong!', Color::FOREGROUND_COLOR_RED);
        CmdRunner::eol();
        CmdRunner::echo($this->optionParseException->getMessage(), Color::FOREGROUND_COLOR_RED);
        CmdRunner::eol();

        $this->help();

        return true;
    }

    public function help(): void {
        (new HelpRunner($this))->run();
    }

    public static function extend(string $cmd): Cmd {
        return new class($cmd) extends Cmd {
        };
    }

    public static function root(): Cmd {
        return self::extend('__root');
    }
}