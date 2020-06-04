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
        }

    }

    /**
     * @param App $app
     * @param array $argv
     * @return Cmd|null
     */
    public static function route(App $app, array $argv): ?Cmd {

        $cmd = $app->setup();

        $appspecs = self::createSpecs($cmd);

        $parser = new ContinuousOptionParser($appspecs);

        try {
            $cmd->options = $parser->parse($argv);
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

                $options_parsed = $cmd->options !== null;

                if (!$options_parsed) {

                    $cmd->options = new OptionResult();

                    if (!empty($cmd->params)) {

                        $specs = self::createSpecs($cmd);

                        $parser->setSpecs($specs);

                        try {
                            $cmd->options = $parser->continueParse();
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

    private static function createSpecs(Cmd $cmd): OptionCollection {

        $params = (array)$cmd->params;

        $specs = new OptionCollection();

        foreach ($params as $k => $v) {
            $opt = $specs->add($k, $v['description']);
            if (array_key_exists('isa', $v)) {
                $opt->isa($v['isa']);
            }
            if (array_key_exists('default', $v)) {
                $opt->defaultValue($v['default']);
            }
        }

        return $specs;
    }


}