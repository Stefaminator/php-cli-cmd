<?php

namespace Stefaminator\Cli\Test;

use Exception;
use \PHPUnit\Framework\TestCase;
use Stefaminator\Cli\AppParser;
use Stefaminator\Cli\Cmd;
use Stefaminator\Cli\Test\Resources\TestApp1;


final class UnitTest extends TestCase {

    public function testNoOptions(): void {
        $app = new TestApp1();

        $argv = ['example2.php'];

        $cmd = AppParser::route($app, $argv);

        $this->assertInstanceOf(Cmd::class, $cmd);

        $this->assertSame('__root', $cmd->cmd);

        $this->assertSame(0, $cmd->optionResult->count());

        $this->assertNull($cmd->arguments);
    }

    public function testHelpFlag(): void {
        $app = new TestApp1();

        $argv = ['example2.php', '--help'];

        $cmd = AppParser::route($app, $argv);

        $this->assertInstanceOf(Cmd::class, $cmd);

        $this->assertSame('__root', $cmd->cmd);

        $this->assertTrue($cmd->optionResult->get('help'));

        $this->assertSame('cmd',$cmd->getMethodName());
    }

    public function testHelpCmd(): void {
        $app = new TestApp1();

        $argv = ['example2.php', 'help', 'list'];

        $cmd = AppParser::route($app, $argv);

        $this->assertInstanceOf(Cmd::class, $cmd);

        $this->assertSame('help', $cmd->cmd);

        $this->assertSame(0, $cmd->optionResult->count());

        $this->assertSame(['list'], $cmd->arguments);

        $this->assertSame('cmdHelp',$cmd->getMethodName());
    }

    public function testListXmlFlag(): void {
        $app = new TestApp1();

        $argv = ['example2.php', 'list', '--xml'];

        $cmd = AppParser::route($app, $argv);

        $this->assertInstanceOf(Cmd::class, $cmd);

        $this->assertSame('list', $cmd->cmd);

        $this->assertSame(2, $cmd->optionResult->count());

        $this->assertTrue($cmd->optionResult->get('xml'));

        $this->assertSame('txt', $cmd->optionResult->get('format'));

        $this->assertNull($cmd->arguments);

        $this->assertSame('cmdList',$cmd->getMethodName());
    }

    public function testListFormatOption(): void {
        $app = new TestApp1();

        $argv = ['example2.php', 'list', '--format', 'json'];

        $cmd = AppParser::route($app, $argv);

        $this->assertInstanceOf(Cmd::class, $cmd);

        $this->assertSame('list', $cmd->cmd);

        $this->assertSame(1, $cmd->optionResult->count());

        $this->assertSame('json', $cmd->optionResult->get('format'));

        $this->assertNull($cmd->arguments);

        $this->assertSame('cmdList',$cmd->getMethodName());
    }

    public function testListInvalidFlag(): void {
        $app = new TestApp1();

        $argv = ['example2.php', 'list', '--invalid'];

        $cmd = AppParser::route($app, $argv);

        $this->assertInstanceOf(Cmd::class, $cmd);

        $this->assertSame('list', $cmd->cmd);

        $this->assertSame(0, $cmd->optionResult->count());

        $this->assertNull($cmd->optionResult->get('xml'));

        $this->assertNull($cmd->arguments);

        $this->assertInstanceOf(Exception::class, $cmd->optionParseException);

        $this->assertEquals('Invalid option: --invalid', $cmd->optionParseException->getMessage());

        $this->assertSame('cmdList',$cmd->getMethodName());
    }

    public function testShow(): void {
        $app = new TestApp1();

        $argv = ['example2.php', 'show'];

        $cmd = AppParser::route($app, $argv);

        $this->assertInstanceOf(Cmd::class, $cmd);

        $this->assertSame('show', $cmd->cmd);

        $this->assertSame(0, $cmd->optionResult->count());

        $this->assertSame('cmdShow',$cmd->getMethodName());

        $this->assertNull($cmd->arguments);
    }

    public function testShowStats(): void {
        $app = new TestApp1();

        $argv = ['example2.php', 'show', 'stats', '--start', '2019-01-01'];

        $cmd = AppParser::route($app, $argv);

        $this->assertInstanceOf(Cmd::class, $cmd);

        $this->assertSame('stats', $cmd->cmd);

        $this->assertSame('show', $cmd->parent->cmd);

        $this->assertSame(1, $cmd->optionResult->count());

//        $this->assertSame(\DateTime::createFromFormat('Y-m-d', '2019-01-01'), $cmd->options->get('start'));

        $this->assertSame('cmdShowStats',$cmd->getMethodName());

        $this->assertNull($cmd->arguments);
    }
}