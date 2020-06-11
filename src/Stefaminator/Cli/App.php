<?php


namespace Stefaminator\Cli;


use Exception;
use GetOptionKit\ContinuousOptionParser;
use GetOptionKit\OptionResult;

abstract class App {

    /**
     * @return CmdRunner
     */
    abstract public function setup(): CmdRunner;


    protected function createRootCmd(CmdRunner $runner): CmdRunner {
        $runner->init();
        return $runner;
    }

    protected function createSubCmd(string $cmd, CmdRunner $runner): CmdRunner {
        $runner->cmd = $cmd;
        $runner->init();
        return $runner;
    }


    public function run(): void {
        global $argv;

        try {
            $runner = $this->parse($argv);

            if ($runner !== null) {

                if ($runner->handleOptionParseException()) {
                    return;
                }

                $runner->run();

            }
        } catch (Exception $e) {

            CmdRunner::eol();
            CmdRunner::echo('Uups, someting went wrong!', Color::FOREGROUND_COLOR_RED);
            CmdRunner::eol();
            CmdRunner::echo($e->getMessage(), Color::FOREGROUND_COLOR_RED);
            CmdRunner::eol();
        }

    }

    /**
     * @param App $app
     * @param array $argv
     * @return CmdRunner
     */
    public function parse(array $argv): CmdRunner {

        $runner = $this->setup();

        $appspecs = $runner->optionCollection();

        $parser = new ContinuousOptionParser($appspecs);

        try {
            $runner->optionResult = $parser->parse($argv);

        } catch (Exception $e) {

            $runner->optionParseException = $e;

            return $runner;
        }

        while (!$parser->isEnd()) {

            $currentArgument = $parser->getCurrentArgument();

            $subcommand = $runner->getChildNode($currentArgument);

            if ($subcommand !== null) {

                $runner = $subcommand;

                try {
                    $this->parseSubcommand($parser, $runner);
                } catch (Exception $e) {
                    $runner->optionParseException = $e;
                    return $runner;
                }

            } else {
                $runner->arguments[] = $parser->advance();
            }
        }

        return $runner;
    }

    /**
     * @param ContinuousOptionParser $parser
     * @param CmdRunner $runner
     */
    private function parseSubcommand(ContinuousOptionParser $parser, CmdRunner $runner): void {

        $parser->advance();

        $runner->optionResult = new OptionResult();

        if (!empty($runner->optSpecs)) {

            $specs = $runner->optionCollection();

            $parser->setSpecs($specs);

            $runner->optionResult = $parser->continueParse();
        }
    }
}