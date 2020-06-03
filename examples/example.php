<?php

require __DIR__ . '/../vendor/autoload.php';

use Stefaminator\Cli\AppParser;
use Stefaminator\Cli\Test\Resources\TestApp1;

AppParser::run(new TestApp1());