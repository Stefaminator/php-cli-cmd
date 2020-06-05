<?php


namespace Stefaminator\Cli;


abstract class App {

    public const EOL = "\n";

    /**
     * @return Cmd
     */
    abstract public function setup(): Cmd;

    public static function eol(): void {
        echo self::EOL;
    }

    public static function echo(string $str, ?string $foreground_color = null): void {

        $lines = preg_split("/\r\n|\n|\r/", $str);

        $output = [];
        foreach($lines as $line) {
            if($foreground_color !== null) {
                $line = Color::getColoredString($line, $foreground_color);
            }
            $output[] = $line;
        }

        echo implode(self::EOL, $output);
    }


}