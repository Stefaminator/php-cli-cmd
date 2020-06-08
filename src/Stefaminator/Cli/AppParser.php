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

                if ($cmd->handleOptionParseException()) {
                    return;
                }

                if (self::callRunner($cmd)) {
                    return;
                }

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
     * @param Cmd $cmd
     * @return bool
     * @throws Exception
     */
    private static function callRunner(Cmd $cmd): bool {

        $runner = $cmd->getRunner();

        if($runner !== null) {
            $runner->run();
            return true;
        }

        return false;
    }

    /**
     * @param App $app
     * @param array $argv
     * @return Cmd
     */
    public static function parse(App $app, array $argv): Cmd {

        $cmd = $app->setup();

        $appspecs = $cmd->getOptionCollection();

        $parser = new ContinuousOptionParser($appspecs);

        try {
            $cmd->optionResult = $parser->parse($argv);

        } catch (Exception $e) {

            $cmd->optionParseException = $e;

            return $cmd;
        }

        while (!$parser->isEnd()) {

            $currentArgument = $parser->getCurrentArgument();

            $subcommand = self::getSubcommand($currentArgument, $cmd);

            if ($subcommand !== null) {

                $cmd = $subcommand;

                try {
                    self::parseSubcommand($parser, $cmd);
                } catch (Exception $e) {
                    $cmd->optionParseException = $e;
                    return $cmd;
                }

            } else {
                $cmd->arguments[] = $parser->advance();
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

        $cmd->optionResult = new OptionResult();

        if (!empty($cmd->optionSpecs)) {

            $specs = $cmd->getOptionCollection();

            $parser->setSpecs($specs);

            $cmd->optionResult = $parser->continueParse();
        }
    }


}