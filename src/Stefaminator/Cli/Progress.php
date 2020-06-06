<?php


namespace Stefaminator\Cli;


class Progress {


    public static function showStatus(int $done, int $total, int $size = 30): void {

        static $start_time = NULL;

        // if we go over our bound, just ignore it
        if ($done > $total) {
            return;
        }

        if (empty($start_time)) {
            $start_time = time();
        }

        $now = time();

        $factor = self::getFactor($total, $done);

        $status_bar_pb = self::getStatusbar($factor, $size);

        $status_bar = "\r";
        $status_bar .= Color::getColoredString($status_bar_pb, Color::FOREGROUND_COLOR_GREEN, Color::BACKGROUND_COLOR_LIGHT_GRAY);

        $disp = number_format($factor * 100);

        $status_bar_percent = str_pad("$disp%", 5, ' ', STR_PAD_LEFT);
        $status_bar .= Color::getColoredString($status_bar_percent, Color::FOREGROUND_COLOR_GREEN);

        $status_bar_done = " $done/$total";

        $status_bar .= Color::getColoredString($status_bar_done, Color::FOREGROUND_COLOR_YELLOW);

        $rate = self::getRate($now - $start_time, $done);
        $left = $total - $done;
        $eta = round($rate * $left, 2);

        $elapsed = $now - $start_time;

        $status_bar .= ' remaining: ' . number_format($eta) . ' sec. elapsed: ' . number_format($elapsed) . ' sec.';

        echo "$status_bar ";

        flush();

        // when done, send a newline
        if ($done === $total) {
            echo "\n";
            $start_time = NULL;
        }
    }

    private static function getStatusbar(float $factor, int $size): string {

        $bar = (int)floor($factor * $size);

        $status_bar_pb = str_repeat('█', $bar);
        if ($bar < $size) {
            $status_bar_pb .= '█';
            $status_bar_pb .= str_repeat(' ', $size - $bar);
        } else {
            $status_bar_pb .= '█';
        }

        return $status_bar_pb;
    }

    private static function getFactor(int $total, int $done): float {

        return (double)(($total === 0) ? 1 : ($done / $total));
    }

    private static function getRate(int $seconds, int $done): float {

        return (double)(($done === 0) ? 0 : ($seconds / $done));
    }
}