<?php


namespace Stefaminator\Cli;


class Help extends Cmd {

    /**
     * @var Cmd
     */
    public $runner;

    public static $header = <<<EOT

              o       
           ` /_\ '    
          - (o o) -   
--------ooO--(_)--Ooo--------
          Need help?
-----------------------------
EOT;

    public function __construct(Cmd $runner) {
        $this->runner = $runner;
        parent::__construct();
    }

    public function init(): void {
    }


    public function run(): void {
        $this->displayHeader();
        $this->displayUsage();
        $this->displayArguments();
        $this->displayOptions();
        $this->displaySubcommands();
        $this->displayHelp();
        self::eol();
    }

    public function displayHeader(): void {

        $help = self::$header;

        self::echo($help, Color::FOREGROUND_COLOR_YELLOW);

        self::eol();
    }

    public function displayUsage(): void {

        $runner = $this->runner;

        $oc = $runner->optionCollection();
        $has_options = !empty($oc->options);

        $arg_usage = $this->getArgumentUsage();

        $has_subcommands = !empty($runner->children);

        self::eol();
        self::echo('Usage: ', Color::FOREGROUND_COLOR_YELLOW);
        self::eol();

        self::echo(
            '  ' .
            ($runner->parent !== null ? $runner->cmd : 'command') .
            ($has_options ? ' [options]' : '') .
            (!empty($arg_usage) ? ' ' . $arg_usage : '') .
            ($has_subcommands ? ' [command]' : '')
        );

        self::eol();
    }

    private function getArgumentUsage() {

        $argSpecs = $this->runner->argSpecs();

        $arg_usage = [];
        foreach ($argSpecs as $k => $v) {
            $arg_usage[] = '[<' . $k . '>]' . (array_key_exists('multiple', $v) ? '...' : '');
        }

        return implode(' ', $arg_usage);
    }

    public function displayArguments(): void {

        $argSpecs = $this->runner->argSpecs();

        $has_arguments = !empty($argSpecs);

        if ($has_arguments) {

            self::eol();
            self::echo('Arguments: ', Color::FOREGROUND_COLOR_YELLOW);
            self::eol();

            foreach ($argSpecs as $spec => $config) {

                $s = $spec;
                $s = '  ' . str_pad($s, 20, ' ');
                self::echo($s, Color::FOREGROUND_COLOR_GREEN);

                $s = ' ' . (array_key_exists('description', $config) ? $config['description'] : '');
                self::echo($s);

                self::eol();
            }

        }

    }

    public function displayOptions(): void {

        $oc = $this->runner->optionCollection();
        $has_options = !empty($oc->options);

        if ($has_options) {

            self::eol();
            self::echo('Options: ', Color::FOREGROUND_COLOR_YELLOW);
            self::eol();

            foreach ($oc->options as $option) {

                $s = '    ';
                if (!empty($option->short)) {
                    $s = '-' . $option->short . ', ';
                }
                $s .= '--' . $option->long;

                $s = '  ' . str_pad($s, 20, ' ');
                self::echo($s, Color::FOREGROUND_COLOR_GREEN);

                $s = ' ' . $option->desc;
                self::echo($s);

                if ($option->defaultValue) {
                    $s = ' [default: ' . $option->defaultValue . ']';
                    self::echo($s, Color::FOREGROUND_COLOR_YELLOW);
                }

                self::eol();
            }
        }

    }

    public function displaySubcommands(): void {

        $subcommands = $this->runner->children;

        $has_subcommands = !empty($subcommands);

        if ($has_subcommands) {

            self::eol();
            self::echo('Available commands: ', Color::FOREGROUND_COLOR_YELLOW);
            self::eol();

            foreach ($subcommands as $_runner) {

                $s = '  ' . str_pad($_runner->cmd, 20, ' ');
                self::echo($s, Color::FOREGROUND_COLOR_GREEN);

                $s = ' ' . $_runner->description();
                self::echo($s);

                self::eol();
            }
        }
    }

    public function displayHelp(): void {

        ob_start();
        $this->runner->help();
        $help = ob_get_clean();

        if (empty($help)) {
            return;
        }

        self::eol();
        self::echo('Help: ', Color::FOREGROUND_COLOR_YELLOW);
        self::eol();

        echo $help;
    }

}