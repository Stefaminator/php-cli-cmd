<?php


namespace Stefaminator\Cli;


class Progress {


    public static function showStatus(int $done, int $total, int $size = 30): void {

        static $start_time = NULL;

        #

        // if we go over our bound, just ignore it
        if ($done > $total) {
            return;
        }
        if (empty($start_time)) {
            $start_time = time();
        }
        $now = time();

        if ($total === 0) {
            $perc = 1;
        } else {
            $perc = (double)($done / $total);
        }
        $bar = floor($perc * $size);

        $status_bar = "\r";

        $status_bar_pb = str_repeat('█', $bar);
        if ($bar < $size) {
            $status_bar_pb .= '█';
            $status_bar_pb .= str_repeat(' ', $size - $bar);
        } else {
            $status_bar_pb .= '█';
        }

        $status_bar .= Color::getColoredString($status_bar_pb, Color::FOREGROUND_COLOR_GREEN, Color::BACKGROUND_COLOR_LIGHT_GRAY);


        $disp = number_format($perc * 100);

        $status_bar_percent = str_pad("$disp%", 5, ' ', STR_PAD_LEFT);
        $status_bar .= Color::getColoredString($status_bar_percent, Color::FOREGROUND_COLOR_GREEN);

        $status_bar_done = " $done/$total";

        $status_bar .= Color::getColoredString($status_bar_done, Color::FOREGROUND_COLOR_YELLOW);

        $rate = ($done === 0) ? 0 : (($now - $start_time) / $done);
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
}