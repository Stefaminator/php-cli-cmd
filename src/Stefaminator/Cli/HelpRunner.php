<?php


namespace Stefaminator\Cli;


class HelpRunner extends CmdRunner {

    public static $header = <<<EOT

              o       
           ` /_\ '    
          - (o o) -   
--------ooO--(_)--Ooo--------
          Need help?
-----------------------------
EOT;


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

        $cmd = $this->getCmd();

        $oc = $cmd->getOptionCollection();
        $has_options = !empty($oc->options);

        $arg_usage = $this->getArgumentUsage();

        $has_subcommands = !empty($cmd->subcommands);

        self::eol();
        self::echo('Usage: ', Color::FOREGROUND_COLOR_YELLOW);
        self::eol();

        self::echo(
            '  ' .
            ($cmd->parent !== null ? $cmd->cmd : 'command') .
            ($has_options ? ' [options]' : '') .
            (!empty($arg_usage) ? ' ' . $arg_usage : '') .
            ($has_subcommands ? ' [command]' : '')
        );

        self::eol();
    }

    private function getArgumentUsage() {

        $cmd = $this->getCmd();

        $arg_usage = [];
        foreach ($cmd->argumentSpecs as $k => $v) {
            $arg_usage[] = '[<' . $k . '>]' . (array_key_exists('multiple', $v) ? '...' : '');
        }

        return implode(' ', $arg_usage);
    }

    public function displayArguments(): void {

        $cmd = $this->getCmd();

        $has_arguments = !empty($cmd->argumentSpecs);

        if ($has_arguments) {

            self::eol();
            self::echo('Arguments: ', Color::FOREGROUND_COLOR_YELLOW);
            self::eol();

            foreach ($cmd->argumentSpecs as $argumentSpec => $config) {

                $s = $argumentSpec;
                $s = '  ' . str_pad($s, 20, ' ');
                self::echo($s, Color::FOREGROUND_COLOR_GREEN);

                $s = ' ' . (array_key_exists('description', $config) ? $config['description'] : '');
                self::echo($s);

                self::eol();
            }

        }

    }

    public function displayOptions(): void {

        $cmd = $this->getCmd();

        $oc = $cmd->getOptionCollection();
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

        $cmd = $this->getCmd();

        $has_subcommands = !empty($cmd->subcommands);

        if ($has_subcommands) {

            self::eol();
            self::echo('Available commands: ', Color::FOREGROUND_COLOR_YELLOW);
            self::eol();

            foreach ($cmd->subcommands as $_cmd) {

                $s = '  ' . str_pad($_cmd->cmd, 20, ' ');
                self::echo($s, Color::FOREGROUND_COLOR_GREEN);

                $s = ' ' . $_cmd->descr;
                self::echo($s);

                self::eol();
            }
        }
    }

    public function displayHelp(): void {

        $cmd = $this->getCmd();

        $runner = $cmd->getRunner();

        if ($runner === null) {
            return;
        }

        ob_start();
        $runner->help();
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