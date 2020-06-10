<?php


namespace Stefaminator\Cli;


use Exception;
use GetOptionKit\ContinuousOptionParser;
use GetOptionKit\OptionResult;

class AppParser {

    public static function run(App $app): void {
        global $argv;

        try {
            $cmd = self::parse($app, $argv);

            if ($cmd !== null) {

                $runner = $cmd->runner();

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
     * @return Cmd
     */
    public static function parse(App $app, array $argv): Cmd {

        $cmd = $app->setup();

        $runner = $cmd->runner();

        $appspecs = $runner->optionCollection();

        $parser = new ContinuousOptionParser($appspecs);

        try {
            $runner->optionResult = $parser->parse($argv);

        } catch (Exception $e) {

            $runner->optionParseException = $e;

            return $cmd;
        }

        while (!$parser->isEnd()) {

            $currentArgument = $parser->getCurrentArgument();

            $subcommand = self::getSubcommand($currentArgument, $cmd);

            if ($subcommand !== null) {

                $cmd = $subcommand;

                $runner = $cmd->runner();

                try {
                    self::parseSubcommand($parser, $cmd);
                } catch (Exception $e) {
                    $runner->optionParseException = $e;
                    return $cmd;
                }

            } else {
                $runner->arguments[] = $parser->advance();
            }
        }

        return $cmd;
    }

    /**
     * @param string $argument
     * @param Cmd $cmd
     * @return Cmd|null
     */
    private static function getSubcommand(string $argument, Cmd $cmd): ?Cmd {

        $subcommand = null;
        if ($cmd->hasSubCmd($argument)) {
            $subcommand = $cmd->getSubCmd($argument);
        }

        return $subcommand;
    }

    /**
     * @param ContinuousOptionParser $parser
     * @param Cmd $cmd
     * @throws Exception
     */
    private static function parseSubcommand(ContinuousOptionParser $parser, Cmd $cmd): void {

        $parser->advance();

        $runner = $cmd->runner();

        $runner->optionResult = new OptionResult();

        if (!empty($runner->optSpecs)) {

            $specs = $runner->optionCollection();

            $parser->setSpecs($specs);

            $runner->optionResult = $parser->continueParse();
        }
    }


}