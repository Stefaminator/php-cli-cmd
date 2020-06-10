<?php


namespace Stefaminator\Cli;


use Exception;
use GetOptionKit\OptionCollection;
use GetOptionKit\OptionResult;

abstract class CmdRunner {

    /**
     * @var Cmd
     */
    private $cmd;

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
     * @param Cmd $cmd
     */
    public function __construct(Cmd $cmd = null) {
        if ($cmd !== null) {
            $this->cmd = $cmd;
            return;
        }
        $this->cmd = new Cmd('__root', $this);
    }

    /**
     * @param Cmd $cmd
     */
    public function init(Cmd $cmd): void {
        $this->cmd = $cmd;
    }

    /**
     * @return Cmd
     */
    public function cmd(): Cmd {
        return $this->cmd;
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
        (new HelpRunner($this->cmd))->run();
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