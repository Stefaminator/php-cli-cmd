<?php


namespace Stefaminator\Cli;


use Exception;
use GetOptionKit\OptionCollection;
use GetOptionKit\OptionResult;

abstract class Cmd {

    /**
     * @var Cmd
     */
    public $parent;

    /**
     * @var Cmd[]
     */
    public $children = [];

    /**
     * @var string
     */
    public $cmd;

    /**
     * @var string
     */
    private $description = '';

    /**
     * @var array
     */
    private $argSpecs = [];

    /**
     * @var string[]
     */
    public $arguments = [];

    /**
     * @var array
     */
    public $optSpecs = [];

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
     * The constructor.
     * @param string|null $cmd
     */
    public function __construct(string $cmd = null) {
        $this->cmd = $cmd;
        $this->init();
    }


    public function runHelp(): void {
        (new Help($this))->run();
    }

    /**
     * Run the cmd
     */
    abstract public function init(): void;

    /**
     * Run the cmd
     */
    abstract public function run(): void;

    /**
     * Overwrite this method for extended help
     */
    public function help(): void {

    }

    public function description(): string {
        return $this->description;
    }

    public function arguments(): array {
        return $this->arguments;
    }

    public function argSpecs(): array {
        return $this->argSpecs;
    }

    public function optionCollection(): OptionCollection {

        if ($this->optionCollection !== null) {
            return $this->optionCollection;
        }

        $specs = (array)$this->optSpecs;

        $collection = new OptionCollection();

        foreach ($specs as $k => $v) {
            try {
                $this->fillOptionCollection($collection, $k, $v);
            } catch (Exception $e) {
                // Option Specs seems to be invalid.
                // Continue with other options without interruption.
            }
        }

        $this->optionCollection = $collection;
        return $this->optionCollection;
    }

    /**
     * @param OptionCollection $collection
     * @param string $spec
     * @param array $config
     * @throws Exception
     */
    private function fillOptionCollection(OptionCollection $collection, string $spec, array $config): void {

        $opt = $collection->add($spec, $config['description']??'');

        if (array_key_exists('isa', $config)) {
            if(array_key_exists('regex', $config) && strtolower($config['isa']) === 'regex') {
                $opt->isa('regex', $config['regex']);
            } else {
                $opt->isa($config['isa']);
            }
        }

        if (array_key_exists('default', $config)) {
            $opt->defaultValue($config['default']);
            return;
        }

        if (array_key_exists('incremental', $config) && $config['incremental'] === true) {
            $opt->incremental();
            return;
        }

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
            foreach ($keys as $key) {
                $r[$key] = $this->getProvidedOption($key);
            }
        }
        return $r;
    }

    /**
     * @param string $key
     * @return bool
     */
    public function hasProvidedOption(string $key): bool {
        return $this->optionResult !== null && $this->optionResult->has($key);
    }

    public function hasChild(string $cmd): bool {
        return array_key_exists($cmd, $this->children);
    }

    public function getChild(string $cmd): ?self {
        if ($this->hasChild($cmd)) {
            return $this->children[$cmd];
        }
        return null;
    }

    public function addChild(Cmd $runner): self {

        $runner->parent = $this;
        $this->children[$runner->cmd] = $runner;

        return $this;
    }

    protected function setDescription(string $description): self {
        $this->description = $description;
        return $this;
    }

    protected function addOption(string $specString, array $config): self {

        $this->optSpecs[$specString] = $config;

        return $this;
    }

    protected function addArgument(string $specString, array $config): self {

        foreach ($this->argSpecs as $k => $v) {
            if (array_key_exists('multiple', $v)) {
                unset($this->argSpecs[$k]['multiple']);
            }
        }

        $config['index'] = count($this->argSpecs);

        $this->argSpecs[$specString] = $config;

        return $this;
    }


    public function handleOptionParseException(): bool {

        if ($this->optionParseException === null) {
            return false;
        }

        self::eol();
        self::echo('Uups, something went wrong!', Color::FOREGROUND_COLOR_RED);
        self::eol();
        self::echo($this->optionParseException->getMessage(), Color::FOREGROUND_COLOR_RED);
        self::eol();

        $this->runHelp();

        return true;
    }


    public const EOL = "\n";


    public static function eol(): void {
        echo self::EOL;
    }

    public static function echo(string $str, ?string $foreground_color = null): void {

        $lines = preg_split("/\r\n|\n|\r/", $str);

        $output = [];
        foreach ($lines as $line) {
            if ($foreground_color !== null) {
                $line = Color::getColoredString($line, $foreground_color);
            }
            $output[] = $line;
        }

        echo implode(self::EOL, $output);
    }

}