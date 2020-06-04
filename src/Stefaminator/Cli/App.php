<?php


namespace Stefaminator\Cli;


abstract class App {

    public const EOL = "\n";

    public const PADDING = "  ";

    /**
     * @return Cmd
     */
    abstract public function setup(): Cmd;

    public static function eol(): void {
        echo self::EOL;
    }

    public static function echo(string $str, ?string $foreground_color = null): void {

        $lines = preg_split("/\r\n|\n|\r/", $str);

        foreach($lines as $line) {
            if($foreground_color === null) {
                echo self::PADDING . $line . self::EOL;
            } else {
                echo self::PADDING . Color::getColoredString($line, $foreground_color). self::EOL;
            }
        }
    }


}