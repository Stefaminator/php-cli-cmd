<?php


namespace Stefaminator\Cli\Test;


use PHPUnit\Framework\TestCase;
use Stefaminator\Cli\AppParser;
use Stefaminator\Cli\Cmd;
use Stefaminator\Cli\Test\Resources\TestApp2;

final class TestApp2Test extends TestCase {

    public function testMainNoOptionsNoArgs(): void {
        $app = new TestApp2();

        $argv = ['testapp2.php'];

        $cmd = AppParser::route($app, $argv);

        $this->assertInstanceOf(Cmd::class, $cmd);

        $this->assertSame('__root', $cmd->cmd);

        /**
         * should be 1 because name-option has a default value
         */
        $this->assertSame(1, $cmd->optionResult->count());

        $this->assertEmpty($cmd->arguments);
    }

    public function testMainNoOptionOneArgument(): void {
        $app = new TestApp2();

        $argv = ['testapp2.php', 'myarg'];

        $cmd = AppParser::route($app, $argv);

        $this->assertInstanceOf(Cmd::class, $cmd);

        $this->assertSame('__root', $cmd->cmd);

        /**
         * should be 1 because name-option has a default value
         */
        $this->assertSame(1, $cmd->optionResult->count());

        $this->assertSame(['myarg'], $cmd->arguments);
    }

    public function testMainVerboseFlagOneArgument(): void {
        $app = new TestApp2();

        $argv = ['testapp2.php', '-v', 'myarg'];

        $cmd = AppParser::route($app, $argv);

        $this->assertInstanceOf(Cmd::class, $cmd);

        $this->assertSame('__root', $cmd->cmd);

        /**
         * should be 2 because name-option has a default value
         */
        $this->assertSame(2, $cmd->optionResult->count());

        $this->assertSame(['myarg'], $cmd->arguments);
    }


    public function testMainVerboseFlagNameOptionOneArgument(): void {
        $app = new TestApp2();

        $argv = ['testapp2.php', '-v', '--name=jaqueline', 'myarg'];

        $cmd = AppParser::route($app, $argv);

        $this->assertInstanceOf(Cmd::class, $cmd);

        $this->assertSame('__root', $cmd->cmd);

        /**
         * should be 2 because name-option has a default value
         */
        $this->assertSame(2, $cmd->optionResult->count());

        $this->assertSame(['myarg'], $cmd->arguments);
    }


    public function testMainVerboseFlagNameOptionTwoArguments(): void {
        $app = new TestApp2();

        $argv = ['testapp2.php', '-v', '--name=jaqueline', 'bla', 'blu'];

        $cmd = AppParser::route($app, $argv);

        $this->assertInstanceOf(Cmd::class, $cmd);

        $this->assertSame('__root', $cmd->cmd);

        /**
         * should be 2 because name-option has a default value
         */
        $this->assertSame(2, $cmd->optionResult->count());

        $this->assertSame(['bla', 'blu'], $cmd->arguments);
    }

}