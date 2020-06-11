<?php


namespace Stefaminator\Cli;


use Exception;
use GetOptionKit\ContinuousOptionParser;
use GetOptionKit\OptionResult;

abstract class App {

    /**
     * @return Cmd
     */
    abstract public function setup(): Cmd;

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

            Cmd::eol();
            Cmd::echo('Uups, someting went wrong!', Color::FOREGROUND_COLOR_RED);
            Cmd::eol();
            Cmd::echo($e->getMessage(), Color::FOREGROUND_COLOR_RED);
            Cmd::eol();
        }

    }

    /**
     * @param array $argv
     * @return Cmd
     */
    public function parse(array $argv): Cmd {

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

            $subcommand = $runner->getChild($currentArgument);

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
     * @param Cmd $runner
     */
    private function parseSubcommand(ContinuousOptionParser $parser, Cmd $runner): void {

        $parser->advance();

        $runner->optionResult = new OptionResult();

        $specs = $runner->optionCollection();

        $parser->setSpecs($specs);

        $runner->optionResult = $parser->continueParse();
    }
}