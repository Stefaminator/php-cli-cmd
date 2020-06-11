<?php


namespace Stefaminator\Cli;


use Exception;
use GetOptionKit\OptionCollection;
use GetOptionKit\OptionResult;

abstract class CmdRunner {

    /**
     * @var CmdRunner
     */
    public $parentNode;

    /**
     * @var CmdRunner[]
     */
    public $childNodes = [];

    /**
     * @var string
     */
    public $cmd;

    /**
     * @var string
     */
    protected $description = '';

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
     * CmdRunner constructor.
     */
    public function __construct() {
    }

    public function init(): void {
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

    public function hasProvidedOption(string $key): bool {
        return $this->optionResult !== null && $this->optionResult->has($key);
    }

    public function hasChildNode(string $cmd): bool {
        return array_key_exists($cmd, $this->childNodes);
    }

    public function getChildNode(string $cmd): ?CmdRunner {
        if ($this->hasChildNode($cmd)) {
            return $this->childNodes[$cmd];
        }
        return null;
    }


    public function addChildNode(CmdRunner $runner): self {

        $runner->parentNode = $this;

        $this->childNodes[$runner->cmd] = $runner;

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


    public function runHelp(): void {
        (new HelpRunner($this))->run();
    }

    /**
     * Run the cmd
     */
    abstract public function run(): void;

    /**
     * Overwrite this method for extended help
     */
    public function help(): void {

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