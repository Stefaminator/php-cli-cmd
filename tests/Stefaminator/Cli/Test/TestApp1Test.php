<?php

namespace Stefaminator\Cli\Test;

use Exception;
use \PHPUnit\Framework\TestCase;
use Stefaminator\Cli\AppParser;
use Stefaminator\Cli\Cmd;
use Stefaminator\Cli\Test\Resources\TestApp1;


final class TestApp1Test extends TestCase {

    public function testParserShowStats(): void {
        global $argv;

        $argv = explode(' ', 'testapp1.php show stats --start=2020-11-11');

        ob_start();
        $app = new TestApp1();
        AppParser::run($app);
        $out = ob_get_clean();

        $this->assertEquals('2020-11-11', $out);
    }

    public function testParserShowException(): void {
        global $argv;

        $argv = explode(' ', 'testapp1.php show exception');

        ob_start();
        $app = new TestApp1();
        AppParser::run($app);
        $out = ob_get_clean();

        $expected = "
\e[0;31mUups, someting went wrong!\e[0m
\e[0;31mfail\e[0m
";

        $this->assertEquals($expected, $out);
    }

    public function testParserHelp(): void {
        global $argv;

        $argv = explode(' ', 'testapp1.php help');

        ob_start();
        $app = new TestApp1();
        AppParser::run($app);
        $out = ob_get_clean();

        $expected = "
\e[1;33m              o       \e[0m
\e[1;33m           ` /_\ '    \e[0m
\e[1;33m          - (o o) -   \e[0m
\e[1;33m----------ooO--(_)--Ooo----------\e[0m
\e[1;33m          Need help?\e[0m
\e[1;33m---------------------------------  \e[0m

\e[1;33mUsage: \e[0m
  command [options] [command]

\e[1;33mOptions: \e[0m
\e[0;32m  -h, --help          \e[0m Display the command help


\e[1;33mAvailable commands: \e[0m
\e[0;32m  help                \e[0m Displays help for this command.
\e[0;32m  list                \e[0m 
\e[0;32m  show                \e[0m 

";

        $this->assertEquals($expected, $out);
    }

    public function testParserInvalidOption(): void {
        global $argv;

        $argv = explode(' ', 'testapp1.php --invalid');

        ob_start();
        $app = new TestApp1();
        AppParser::run($app);
        $out = ob_get_clean();

        $expected = "
\e[0;31mUups, something went wrong!\e[0m
\e[0;31mInvalid option: --invalid\e[0m

\e[1;33m              o       \e[0m
\e[1;33m           ` /_\ '    \e[0m
\e[1;33m          - (o o) -   \e[0m
\e[1;33m----------ooO--(_)--Ooo----------\e[0m
\e[1;33m          Need help?\e[0m
\e[1;33m---------------------------------  \e[0m

\e[1;33mUsage: \e[0m
  command [options] [command]

\e[1;33mOptions: \e[0m
\e[0;32m  -h, --help          \e[0m Display the command help


\e[1;33mAvailable commands: \e[0m
\e[0;32m  help                \e[0m Displays help for this command.
\e[0;32m  list                \e[0m 
\e[0;32m  show                \e[0m 

";

        $this->assertEquals($expected, $out);
    }

    public function testMainNoOptionsNoArgs(): void {
        $app = new TestApp1();

        $argv = ['example2.php'];

        $cmd = AppParser::parse($app, $argv);

        $this->assertSame('__root', $cmd->cmd);

        $this->assertSame(0, $cmd->optionResult->count());

        $this->assertNull($cmd->getProvidedOption('invalidoption'));
        $this->assertFalse($cmd->hasProvidedOption('invalidoption'));


        $this->assertNull($cmd->getProvidedOption('help'));
        $this->assertFalse($cmd->hasProvidedOption('help'));

        $this->assertEmpty($cmd->arguments);
    }

    public function testMainArguments(): void {
        $app = new TestApp1();

        $argv = ['example2.php', 'myarg'];

        $cmd = AppParser::parse($app, $argv);

        $this->assertInstanceOf(Cmd::class, $cmd);

        $this->assertSame('__root', $cmd->cmd);

        $this->assertSame(0, $cmd->optionResult->count());

        $this->assertSame(['myarg'], $cmd->arguments);
    }


    public function testMainOptionsAndArguments(): void {
        $app = new TestApp1();

        $argv = ['example2.php', '-h', 'myarg'];

        $cmd = AppParser::parse($app, $argv);

        $this->assertInstanceOf(Cmd::class, $cmd);

        $this->assertSame('__root', $cmd->cmd);

        $this->assertTrue($cmd->optionResult->get('help'));

        $this->assertSame(['myarg'], $cmd->arguments);
    }

    public function testMainHelpFlag(): void {
        $app = new TestApp1();

        $argv = ['example2.php', '--help'];

        $cmd = AppParser::parse($app, $argv);

        $this->assertInstanceOf(Cmd::class, $cmd);

        $this->assertSame('__root', $cmd->cmd);

        $this->assertTrue($cmd->optionResult->get('help'));

        $this->assertSame('cmd',$cmd->getMethodName());
    }

    public function testHelpCmd(): void {
        $app = new TestApp1();

        $argv = ['example2.php', 'help', 'list'];

        $cmd = AppParser::parse($app, $argv);

        $this->assertInstanceOf(Cmd::class, $cmd);

        $this->assertSame('help', $cmd->cmd);

        $this->assertSame(0, $cmd->optionResult->count());

        $this->assertSame(['list'], $cmd->arguments);

        $this->assertSame('cmdHelp',$cmd->getMethodName());
    }

    public function testListXmlFlag(): void {
        $app = new TestApp1();

        $argv = ['example2.php', 'list', '--xml'];

        $cmd = AppParser::parse($app, $argv);

        $this->assertInstanceOf(Cmd::class, $cmd);

        $this->assertSame('list', $cmd->cmd);

        $this->assertSame(2, $cmd->optionResult->count());

        $this->assertTrue($cmd->optionResult->get('xml'));

        $this->assertSame('txt', $cmd->optionResult->get('format'));

        $this->assertEmpty($cmd->arguments);

        $this->assertSame('cmdList',$cmd->getMethodName());
    }

    public function testListFormatOption(): void {
        $app = new TestApp1();

        $argv = ['example2.php', 'list', '--format', 'json'];

        $cmd = AppParser::parse($app, $argv);

        $this->assertInstanceOf(Cmd::class, $cmd);

        $this->assertSame('list', $cmd->cmd);

        $this->assertSame(1, $cmd->optionResult->count());

        $this->assertSame('json', $cmd->optionResult->get('format'));

        $this->assertEmpty($cmd->arguments);

        $this->assertSame('cmdList',$cmd->getMethodName());
    }

    public function testListInvalidFlag(): void {
        $app = new TestApp1();

        $argv = ['example2.php', 'list', '--invalid'];

        $cmd = AppParser::parse($app, $argv);

        $this->assertInstanceOf(Cmd::class, $cmd);

        $this->assertSame('list', $cmd->cmd);

        $this->assertSame(0, $cmd->optionResult->count());

        $this->assertNull($cmd->optionResult->get('xml'));

        $this->assertEmpty($cmd->arguments);

        $this->assertInstanceOf(Exception::class, $cmd->optionParseException);

        $this->assertEquals('Invalid option: --invalid', $cmd->optionParseException->getMessage());

        $this->assertSame('cmdList',$cmd->getMethodName());
    }

    public function testShow(): void {
        $app = new TestApp1();

        $argv = ['example2.php', 'show'];

        $cmd = AppParser::parse($app, $argv);

        $this->assertInstanceOf(Cmd::class, $cmd);

        $this->assertSame('show', $cmd->cmd);

        $this->assertSame(0, $cmd->optionResult->count());

        $this->assertSame('cmdShow',$cmd->getMethodName());

        $this->assertEmpty($cmd->arguments);
    }

    public function testShowStats(): void {
        $app = new TestApp1();

        $argv = ['example2.php', 'show', 'stats', '--start', '2019-01-01'];

        $cmd = AppParser::parse($app, $argv);

        $this->assertInstanceOf(Cmd::class, $cmd);

        $this->assertSame('stats', $cmd->cmd);

        $this->assertSame('show', $cmd->parent->cmd);

        $this->assertSame(1, $cmd->optionResult->count());

//        $this->assertSame(\DateTime::createFromFormat('Y-m-d', '2019-01-01'), $cmd->options->get('start'));

        $this->assertSame('cmdShowStats',$cmd->getMethodName());

        $this->assertEmpty($cmd->arguments);
    }
}