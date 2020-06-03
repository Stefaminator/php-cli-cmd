<?php


namespace Stefaminator\Cli;


class Color {

    public const FOREGROUND_COLOR_BLACK = '0;30';
    public const FOREGROUND_COLOR_DARK_GRAY = '1;30';
    public const FOREGROUND_COLOR_BLUE = '0;34';
    public const FOREGROUND_COLOR_LIGHT_BLUE = '1;34';
    public const FOREGROUND_COLOR_GREEN = '0;32';
    public const FOREGROUND_COLOR_LIGHT_GREEN = '1;32';
    public const FOREGROUND_COLOR_CYAN = '0;36';
    public const FOREGROUND_COLOR_LIGHT_CYAN = '1;36';
    public const FOREGROUND_COLOR_RED = '0;31';
    public const FOREGROUND_COLOR_LIGHT_RED = '1;31';
    public const FOREGROUND_COLOR_PURPLE = '0;35';
    public const FOREGROUND_COLOR_LIGHT_PURPLE = '1;35';
    public const FOREGROUND_COLOR_BROWN = '0;33';
    public const FOREGROUND_COLOR_YELLOW = '1;33';
    public const FOREGROUND_COLOR_LIGHT_GRAY = '0;37';
    public const FOREGROUND_COLOR_WHITE = '1;37';

    public const BACKGROUND_COLOR_BLACK = '40';
    public const BACKGROUND_COLOR_RED = '41';
    public const BACKGROUND_COLOR_GREEN = '42';
    public const BACKGROUND_COLOR_YELLOW = '43';
    public const BACKGROUND_COLOR_BLUE = '44';
    public const BACKGROUND_COLOR_PURPLE = '45';
    public const BACKGROUND_COLOR_CYAN = '46';
    public const BACKGROUND_COLOR_LIGHT_GRAY = '47';

    public static function getForegroundColors(): array {
        return [
            self::FOREGROUND_COLOR_BLACK => 'BLACK',
            self::FOREGROUND_COLOR_DARK_GRAY => 'DARK_GRAY',
            self::FOREGROUND_COLOR_BLUE => 'BLUE',
            self::FOREGROUND_COLOR_LIGHT_BLUE => 'LIGHT_BLUE',
            self::FOREGROUND_COLOR_GREEN => 'GREEN',
            self::FOREGROUND_COLOR_LIGHT_GREEN => 'LIGHT_GREEN',
            self::FOREGROUND_COLOR_CYAN => 'CYAN',
            self::FOREGROUND_COLOR_LIGHT_CYAN => 'LIGHT_CYAN',
            self::FOREGROUND_COLOR_RED => 'RED',
            self::FOREGROUND_COLOR_LIGHT_RED => 'LIGHT_RED',
            self::FOREGROUND_COLOR_PURPLE => 'PURPLE',
            self::FOREGROUND_COLOR_LIGHT_PURPLE => 'LIGHT_PURPLE',
            self::FOREGROUND_COLOR_BROWN => 'BROWN',
            self::FOREGROUND_COLOR_YELLOW => 'YELLOW',
            self::FOREGROUND_COLOR_LIGHT_GRAY => 'LIGHT_GRAY',
            self::FOREGROUND_COLOR_WHITE => 'WHITE'
        ];
    }

    public static function getBackgroundColors(): array {
        return [
            self::BACKGROUND_COLOR_BLACK => 'BLACK',
            self::BACKGROUND_COLOR_RED => 'RED',
            self::BACKGROUND_COLOR_GREEN => 'GREEN',
            self::BACKGROUND_COLOR_YELLOW => 'YELLOW',
            self::BACKGROUND_COLOR_BLUE => 'BLUE',
            self::BACKGROUND_COLOR_PURPLE => 'PURPLE',
            self::BACKGROUND_COLOR_CYAN => 'CYAN',
            self::BACKGROUND_COLOR_LIGHT_GRAY => 'LIGHT_GRAY'
        ];
    }

    // Returns colored string
    public static function getColoredString($string, $foreground_color = null, $background_color = null): string {

        $colored_string = '';

        // Check if given foreground color found
        if ($foreground_color !== null && array_key_exists($foreground_color, self::getForegroundColors())) {
            $colored_string .= "\033[" . $foreground_color . 'm';
        }
        // Check if given background color found
        if ($background_color !== null && array_key_exists($background_color, self::getBackgroundColors())) {
            $colored_string .= "\033[" . $background_color . 'm';
        }

        // Add string and end coloring
        $colored_string .=  $string . "\033[0m";

        return $colored_string;
    }

    public static function echo($string, $foreground_color = null, $background_color = null): void {
        echo self::getColoredString($string, $foreground_color, $background_color);
    }

    public static function red($string): void {
        echo self::getColoredString($string, self::FOREGROUND_COLOR_RED);
    }

    public static function green($string): void {
        echo self::getColoredString($string, self::FOREGROUND_COLOR_GREEN);
    }

    public static function blue($string): void {
        echo self::getColoredString($string, self::FOREGROUND_COLOR_BLUE);
    }

    public static function yellow($string): void {
        echo self::getColoredString($string, self::FOREGROUND_COLOR_YELLOW);
    }

    public static function purple($string): void {
        echo self::getColoredString($string, self::FOREGROUND_COLOR_PURPLE);
    }

}