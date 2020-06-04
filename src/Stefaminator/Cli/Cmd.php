<?php

namespace Stefaminator\Cli;

use Exception;
use GetOptionKit\OptionResult;
use ReflectionFunction;
use RuntimeException;


class Cmd {

    /**
     * @var Cmd
     */
    public $parent;

    /**
     * @var string
     */
    public $cmd;

    /**
     * @var array
     */
    public $params;

    /**
     * @var OptionResult
     */
    public $options;

    /**
     * @var string[]
     */
    public $arguments;

    /**
     * @var array
     */
    private $cmds = [];

    /**
     * @var Exception
     */
    public $optionParseException;

    /**
     * @var callable|null
     */
    private $callable;


    public function __construct(string $cmd) {
        $this->cmd = $cmd;
        if ($cmd !== 'help') {
            $this->addSubCmd(
                self::extend('help')
                    ->setCallable(static function(Cmd $cmd) {
                        $cmd->parent->help();
                    })
            );
        }
    }

    public function addParam(string $specString, array $config): self {
        $this->params[$specString] = $config;
        return $this;
    }

    public function addSubCmd(Cmd $cmd): self {

//        if (!array_key_exists($cmd->cmd, $this->cmds)) {
            $cmd->parent = $this;
            $this->cmds[$cmd->cmd] = $cmd;
//        }

        return $this;
    }

    /**
     * @param callable $callable
     * @return $this
     */
    public function setCallable(callable $callable): self {

        try {
            $this->callable = $this->validateCallable($callable);

        } catch (Exception $e) {
            echo __METHOD__ . ' has been called with invalid callable: ' . $e->getMessage() . "\n";
        }


        return $this;
    }

    public function existsSubCmd(string $cmd): bool {
        return array_key_exists($cmd, $this->cmds);
    }

    public function getSubCmd(string $cmd): ?Cmd {
        if ($this->existsSubCmd($cmd)) {
            return $this->cmds[$cmd];
        }
        return null;
    }

    public function getMethodName(): string {
        $cmd = $this;
        $pwd = [];

        while ($cmd !== null) {
            $pwd[] = $cmd->parent !== null ? $cmd->cmd : 'cmd';
            $cmd = $cmd->parent;
        }

        $pwd = array_reverse($pwd);

        $pwd_str = '';
        foreach ($pwd as $p) {
            $pwd_str .= ucfirst(strtolower($p));
        }

        return lcfirst($pwd_str);
    }

    public function getCallable(): ?callable {
        return $this->callable;
    }

    public function help(): void {

        $eol = "\n";

        $help = <<<EOT

                  o       
               ` /_\ '    
              - (o o) -   
  ----------ooO--(_)--Ooo----------
                help?
  ---------------------------------
  
EOT;


        Color::yellow($help);

        echo $eol;

        if (!empty($this->params)) {

            echo $eol;
            echo 'Parameters: ' . $eol;

            foreach ($this->params as $k => $v) {

                echo '  ' . str_pad($k, 20, ' ');
                echo ' ' . $v['description'] . $eol;

            }
        }
    }

    public static function extend(string $cmd): Cmd {
        return new class($cmd) extends Cmd {};
    }


    /**
     * @param callable $callable
     * @return callable
     * @throws Exception
     */
    private function validateCallable(callable $callable): callable {

        $check = new ReflectionFunction($callable);
        $parameters = $check->getParameters();

        if (count($parameters) !== 1) {
            throw new RuntimeException('Invalid number of Parameters. Should be 1.');
        }

        $type = $parameters[0]->getType();

        if ($type === null) {
            throw new RuntimeException('Named type of Parameter 1 should be "' . __CLASS__ . '".');
        }

        $tname = $type->getName();

        if ($tname !== __CLASS__) {
            throw new RuntimeException('Named type of Parameter 1 should be "' . __CLASS__ . '".');
        }

        return $callable;
    }
}