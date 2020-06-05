<?php


namespace Stefaminator\Cli;


use Exception;
use GetOptionKit\ContinuousOptionParser;
use GetOptionKit\OptionCollection;
use GetOptionKit\OptionResult;

class AppParser {


    public static function run(App $app): void {
        global $argv;

        try {
            $cmd = self::route($app, $argv);

            if ($cmd !== null) {

                $cmd->handleOptionParseException();

                $callable = $cmd->getCallable();

                if ($callable !== null) {
                    $callable($cmd);
                    return;
                }

                $methodName = $cmd->getMethodName();

                if (method_exists($app, $methodName)) {
                    $app->$methodName($cmd);
                    return;
                }


            }
        } catch (Exception $e) {

            App::eol();

            App::echo('Uups, someting went wrong!', Color::FOREGROUND_COLOR_RED);
            App::echo($e->getMessage(), Color::FOREGROUND_COLOR_RED);
            App::eol();
        }

    }

    /**
     * @param App $app
     * @param array $argv
     * @return Cmd|null
     */
    public static function route(App $app, array $argv): ?Cmd {

        $cmd = $app->setup();

        $appspecs = self::createOptionCollection($cmd);

        $parser = new ContinuousOptionParser($appspecs);

        try {
            $cmd->optionResult = $parser->parse($argv);
        } catch (Exception $e) {
            $cmd->optionParseException = $e;
            return $cmd;
        }

        while (!$parser->isEnd()) {

            $currentArgument = $parser->getCurrentArgument();

            if ($cmd->existsSubCmd($currentArgument)) {

                $parser->advance();

                $cmd = $cmd->getSubCmd($currentArgument);
            }

            if ($cmd instanceof Cmd) {

                $options_parsed = $cmd->optionResult !== null;

                if (!$options_parsed) {

                    $cmd->optionResult = new OptionResult();

                    if (!empty($cmd->optionSpecs)) {

                        $specs = self::createOptionCollection($cmd);

                        $parser->setSpecs($specs);

                        try {
                            $cmd->optionResult = $parser->continueParse();
                        } catch (Exception $e) {
                            $cmd->optionParseException = $e;
                            return $cmd;
                        }

                        continue;
                    }
                }

                if ($options_parsed) {
                    $cmd->arguments[] = $parser->advance();
                }
            }

        }

        return $cmd;
    }

    private static function createOptionCollection(Cmd $cmd): OptionCollection {

        $specs = (array)$cmd->optionSpecs;

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

        return $collection;
    }


}