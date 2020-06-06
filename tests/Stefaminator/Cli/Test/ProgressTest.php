<?php

namespace Stefaminator\Cli\Test;

use Stefaminator\Cli\Progress;
use PHPUnit\Framework\TestCase;

class ProgressTest extends TestCase {

    public function testShowStatus() {

        $total = 100;

        for($done = 0; $done <= $total; $done++) {
            $this->progressbarTest($done, $total);
        }

    }

    private function progressbarTest(int $done, int $total): void {

        $size = 100;

        ob_start();
        Progress::showStatus($done, $total, $size);
        $out = ob_get_clean();

        preg_match('/[█| ]{' . $size . '}/u', $out, $matches);

        $this->assertCount(1, $matches);

        $match = $matches[0];

        preg_match('/[█]+/u', $match, $matches_fill);

        if($done === 0) {
            $this->assertCount(0, $matches_fill);
        } else {
            $this->assertCount(1, $matches_fill);
            $this->assertEquals($done, mb_strlen($matches_fill[0]));
        }


        preg_match('/[ ]+/u', $match, $matches_empty);
        if($done === $total) {
            $this->assertCount(0, $matches_empty);
        } else {
            $this->assertCount(1, $matches_empty);
            $this->assertEquals($total - $done, mb_strlen($matches_empty[0]));
        }
    }


}
