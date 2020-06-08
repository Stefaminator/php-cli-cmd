<?php


namespace Stefaminator\Cli;


abstract class CmdRunner {

    /**
     * @var Cmd
     */
    private $cmd;

    /**
     * CmdRunner constructor.
     * @param Cmd $cmd
     */
    public function __construct(Cmd $cmd = null) {
        if($cmd !== null) {
            $this->cmd = $cmd;
            return;
        }
        $this->cmd = Cmd::root();
    }

    /**
     * @return Cmd
     */
    public function getCmd(): Cmd {
        return $this->cmd;
    }

    /**
     * @param Cmd $cmd
     */
    public function setCmd(Cmd $cmd): void {
        $this->cmd = $cmd;
    }

    /**
     * Run the cmd
     */
    abstract public function run(): void;

    /**
     * Overwrite this method for extended help
     */
    public function help(): void {

    }


    public const EOL = "\n";


    public static function eol(): void {
        echo self::EOL;
    }

    public static function echo(string $str, ?string $foreground_color = null) : void {

        $lines = preg_split("/\r\n|\n|\r/", $str);

        $output = [];
        foreach ($lines as $line) {
            if ($foreground_color !== null) {
                $line = Color::getColoredString($line, $foreground_color);
            }
            $output[] = $line;
        }

        echo implode(self::EOL, $output);
    }

}