<?php


namespace Stefaminator\Cli\Test;


use PHPUnit\Framework\TestCase;
use Stefaminator\Cli\AppParser;
use Stefaminator\Cli\Test\Resources\TestApp2;

final class TestApp2Test extends TestCase {

    public function testMainNoOptionsNoArgs(): void {
        $app = new TestApp2();

        $argv = ['testapp2.php'];

        $cmd = AppParser::parse($app, $argv);

        $runner = $cmd->runner();

        $this->assertSame('__root', $cmd->cmd);

        /**
         * should be 1 because name-option has a default value
         */
        $this->assertSame(1, $runner->optionResult->count());

        $this->assertEmpty($runner->arguments);
    }

    public function testMainNoOptionOneArgument(): void {
        $app = new TestApp2();

        $argv = ['testapp2.php', 'myarg'];

        $cmd = AppParser::parse($app, $argv);

        $runner = $cmd->runner();

        $this->assertSame('__root', $cmd->cmd);

        /**
         * should be 1 because name-option has a default value
         */
        $this->assertSame(1, $runner->optionResult->count());

        $this->assertSame(['myarg'], $runner->arguments);
    }

    public function testMainVerboseFlagOneArgument(): void {
        $app = new TestApp2();

        $argv = ['testapp2.php', '-v', 'myarg'];

        $cmd = AppParser::parse($app, $argv);

        $runner = $cmd->runner();

        $this->assertSame('__root', $cmd->cmd);

        /**
         * should be 2 because name-option has a default value
         */
        $this->assertSame(2, $runner->optionResult->count());

        $this->assertSame(['myarg'], $runner->arguments);
    }


    public function testMainVerboseFlagNameOptionOneArgument(): void {
        $app = new TestApp2();

        $argv = ['testapp2.php', '-v', '--name=jaqueline', 'myarg'];

        $cmd = AppParser::parse($app, $argv);

        $runner = $cmd->runner();

        $this->assertSame('__root', $cmd->cmd);

        /**
         * should be 2 because name-option has a default value
         */
        $this->assertSame(2, $runner->optionResult->count());

        $this->assertSame(['myarg'], $runner->arguments);
    }


    public function testMainVerboseFlagNameOptionTwoArguments(): void {
        $app = new TestApp2();

        $argv = ['testapp2.php', '-v', '--name=jaqueline', 'bla', 'blu'];

        $cmd = AppParser::parse($app, $argv);

        $runner = $cmd->runner();

        $this->assertSame('__root', $cmd->cmd);

        /**
         * should be 2 because name-option has a default value
         */
        $this->assertSame(2, $runner->optionResult->count());

        $this->assertSame(['bla', 'blu'], $runner->arguments);
    }

}