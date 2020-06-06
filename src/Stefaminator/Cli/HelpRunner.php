<?php


namespace Stefaminator\Cli;


class HelpRunner extends CmdRunner {


    public function run(): void {
        $this->displayHeader();
        $this->displayUsage();
        $this->displayOptions();
        $this->displaySubcommands();
    }

    public function displayHeader(): void {


        $help = <<<EOT

              o       
           ` /_\ '    
          - (o o) -   
----------ooO--(_)--Ooo----------
          Need help?
---------------------------------  
EOT;

        App::echo($help, Color::FOREGROUND_COLOR_YELLOW);

        App::eol();
    }

    public function displayUsage(): void {

        $cmd = $this->cmd();

        $oc = $cmd->getOptionCollection();
        $has_options = !empty($oc->options);

        $has_subcommands = !empty($cmd->subcommands);

        App::eol();
        App::echo('Usage: ', Color::FOREGROUND_COLOR_YELLOW);
        App::eol();

        App::echo(
            '  ' .
            ($cmd->parent !== null ? $cmd->cmd : 'command') .
            ($has_options ? ' [options]' : '') .
            ($has_subcommands ? ' [command]' : '')
        );

        App::eol();
    }

    public function displayOptions(): void {

        $cmd = $this->cmd();

        $oc = $cmd->getOptionCollection();
        $has_options = !empty($oc->options);

        if ($has_options) {

            App::eol();
            App::echo('Options: ', Color::FOREGROUND_COLOR_YELLOW);
            App::eol();

            foreach ($oc->options as $option) {

                $s = '    ';
                if(!empty($option->short)) {
                    $s = '-' . $option->short . ', ';
                }
                $s .= '--' . $option->long;

                $s = '  ' . str_pad($s, 20, ' ');
                App::echo($s, Color::FOREGROUND_COLOR_GREEN);

                $s = ' ' . $option->desc;
                App::echo($s);

                if ($option->defaultValue) {
                    $s = ' [default: ' . $option->defaultValue . ']';
                    App::echo($s, Color::FOREGROUND_COLOR_YELLOW);
                }

                App::eol();
            }

            App::eol();
        }

    }

    public function displaySubcommands(): void {

        $cmd = $this->cmd();

        $has_subcommands = !empty($cmd->subcommands);

        if($has_subcommands) {

            App::eol();
            App::echo('Available commands: ', Color::FOREGROUND_COLOR_YELLOW);
            App::eol();

            foreach ($cmd->subcommands as $_cmd) {

                $s = '  ' . str_pad($_cmd->cmd, 20, ' ');
                App::echo($s, Color::FOREGROUND_COLOR_GREEN);

                $s = ' ' . $_cmd->descr;
                App::echo($s);

                App::eol();
            }

            App::eol();
        }
    }

}