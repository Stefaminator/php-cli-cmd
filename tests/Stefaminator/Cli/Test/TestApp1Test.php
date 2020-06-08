<?php

namespace Stefaminator\Cli\Test;

use Exception;
use \PHPUnit\Framework\TestCase;
use Stefaminator\Cli\AppParser;
use Stefaminator\Cli\Test\Resources\TestApp1;


final class TestApp1Test extends TestCase {

    public function testParserShowStats(): void {
        global $argv;

        $argv = explode(' ', 'testapp1.php show stats --start=2020-11-11 -v arg1 arg2 arg3');

        ob_start();
        $app = new TestApp1();
        AppParser::run($app);
        $out = ob_get_clean();
        
        $expected = "\e[1;37m\e[45m2020-11-11\e[0m

\e[0;32m--- VERBOSE OUTPUT ---\e[0m

\e[0;32m  All current options...\e[0m
\e[0;32m    start: {\"year\":2020,\"month\":11,\"day\":11,\"hour\":false,\"minute\":false,\"second\":false,\"fraction\":false,\"warning_count\":0,\"warnings\":[],\"error_count\":0,\"errors\":[],\"is_localtime\":false}\e[0m
\e[0;32m    verbose: true\e[0m

\e[0;32m  All current arguments...\e[0m
\e[0;32m    arg1\e[0m
\e[0;32m    arg2\e[0m
\e[0;32m    arg3\e[0m

";

        $this->assertEquals($expected, $out);
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

    public function testParserHelpCommand(): void {
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
\e[1;33m--------ooO--(_)--Ooo--------\e[0m
\e[1;33m          Need help?\e[0m
\e[1;33m-----------------------------\e[0m

\e[1;33mUsage: \e[0m
  command [options] [command]

\e[1;33mOptions: \e[0m
\e[0;32m  -h, --help          \e[0m Display the command help

\e[1;33mAvailable commands: \e[0m
\e[0;32m  list                \e[0m 
\e[0;32m  show                \e[0m 
\e[0;32m  greetings           \e[0m Display some greetings.
\e[0;32m  help                \e[0m Displays help for this command.

";

        $this->assertEquals($expected, $out);
    }

    public function testParserShowHelloHelpCommand(): void {
        global $argv;

        $argv = explode(' ', 'testapp1.php show hello -h');

        ob_start();
        $app = new TestApp1();
        AppParser::run($app);
        $out = ob_get_clean();

        $expected = "
\e[1;33m              o       \e[0m
\e[1;33m           ` /_\ '    \e[0m
\e[1;33m          - (o o) -   \e[0m
\e[1;33m--------ooO--(_)--Ooo--------\e[0m
\e[1;33m          Need help?\e[0m
\e[1;33m-----------------------------\e[0m

\e[1;33mUsage: \e[0m
  hello [options]

\e[1;33mOptions: \e[0m
\e[0;32m  -h, --help          \e[0m Display the command help
\e[0;32m      --name          \e[0m The stats start date\e[1;33m [default: world]\e[0m

\e[1;33mHelp: \e[0m
\e[0;31mtest\e[0m \e[0;32mtest\e[0m \e[0;34mtest\e[0m \e[1;33mtest\e[0m \e[0;35mtest\e[0m
";

        $this->assertEquals($expected, $out);
    }

    public function testParserGreetingsHelpOption(): void {
        global $argv;

        $argv = explode(' ', 'testapp1.php greetings --help');

        ob_start();
        $app = new TestApp1();
        AppParser::run($app);
        $out = ob_get_clean();

        $expected = "
\e[1;33m              o       \e[0m
\e[1;33m           ` /_\ '    \e[0m
\e[1;33m          - (o o) -   \e[0m
\e[1;33m--------ooO--(_)--Ooo--------\e[0m
\e[1;33m          Need help?\e[0m
\e[1;33m-----------------------------\e[0m

\e[1;33mUsage: \e[0m
  greetings [options] [<event>] [<names>]...

\e[1;33mArguments: \e[0m
\e[0;32m  event               \e[0m The occasion of the greetings, may be xmas, easter, birthday or some custom event.
\e[0;32m  names               \e[0m Multiple names of the greetings receivers.

\e[1;33mOptions: \e[0m
\e[0;32m  -c, --colored       \e[0m Display the greetings colored.
\e[0;32m  -h, --help          \e[0m Displays help for this command.

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
\e[1;33m--------ooO--(_)--Ooo--------\e[0m
\e[1;33m          Need help?\e[0m
\e[1;33m-----------------------------\e[0m

\e[1;33mUsage: \e[0m
  command [options] [command]

\e[1;33mOptions: \e[0m
\e[0;32m  -h, --help          \e[0m Display the command help

\e[1;33mAvailable commands: \e[0m
\e[0;32m  list                \e[0m 
\e[0;32m  show                \e[0m 
\e[0;32m  greetings           \e[0m Display some greetings.
\e[0;32m  help                \e[0m Displays help for this command.

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

        $this->assertSame('__root', $cmd->cmd);

        $this->assertSame(0, $cmd->optionResult->count());

        $this->assertSame(['myarg'], $cmd->arguments);
    }


    public function testMainOptionsAndArguments(): void {
        $app = new TestApp1();

        $argv = ['example2.php', '-h', 'myarg'];

        $cmd = AppParser::parse($app, $argv);

        $this->assertSame('__root', $cmd->cmd);

        $this->assertTrue($cmd->optionResult->get('help'));

        $this->assertSame(['myarg'], $cmd->arguments);
    }

    public function testMainHelpFlag(): void {
        $app = new TestApp1();

        $argv = ['example2.php', '--help'];

        $cmd = AppParser::parse($app, $argv);

        $this->assertSame('__root', $cmd->cmd);

        $this->assertTrue($cmd->optionResult->get('help'));

    }

    public function testHelpCmd(): void {
        $app = new TestApp1();

        $argv = ['example2.php', 'help', 'list'];

        $cmd = AppParser::parse($app, $argv);

        $this->assertSame('help', $cmd->cmd);

        $this->assertSame(0, $cmd->optionResult->count());

        $this->assertSame(['list'], $cmd->arguments);
    }

    public function testListXmlFlag(): void {
        $app = new TestApp1();

        $argv = ['example2.php', 'list', '--xml'];

        $cmd = AppParser::parse($app, $argv);

        $this->assertSame('list', $cmd->cmd);

        $this->assertSame(2, $cmd->optionResult->count());

        $this->assertTrue($cmd->optionResult->get('xml'));

        $this->assertSame('txt', $cmd->optionResult->get('format'));

        $this->assertEmpty($cmd->arguments);

    }

    public function testListFormatOption(): void {
        $app = new TestApp1();

        $argv = ['example2.php', 'list', '--format', 'json'];

        $cmd = AppParser::parse($app, $argv);

        $this->assertSame('list', $cmd->cmd);

        $this->assertSame(1, $cmd->optionResult->count());

        $this->assertSame('json', $cmd->optionResult->get('format'));

        $this->assertEmpty($cmd->arguments);
    }

    public function testListInvalidFlag(): void {
        $app = new TestApp1();

        $argv = ['example2.php', 'list', '--invalid'];

        $cmd = AppParser::parse($app, $argv);

        $this->assertSame('list', $cmd->cmd);

        $this->assertSame(0, $cmd->optionResult->count());

        $this->assertNull($cmd->optionResult->get('xml'));

        $this->assertEmpty($cmd->arguments);

        $this->assertInstanceOf(Exception::class, $cmd->optionParseException);

        $this->assertEquals('Invalid option: --invalid', $cmd->optionParseException->getMessage());

    }

    public function testShow(): void {
        $app = new TestApp1();

        $argv = ['example2.php', 'show'];

        $cmd = AppParser::parse($app, $argv);

        $this->assertSame('show', $cmd->cmd);

        $this->assertSame(0, $cmd->optionResult->count());

        $this->assertEmpty($cmd->arguments);
    }

    public function testShowStats(): void {
        $app = new TestApp1();

        $argv = ['example2.php', 'show', 'stats', '--start', '2019-01-01'];

        $cmd = AppParser::parse($app, $argv);

        $this->assertSame('stats', $cmd->cmd);

        $this->assertSame('show', $cmd->parent->cmd);

        $this->assertSame(1, $cmd->optionResult->count());

//        $this->assertSame(\DateTime::createFromFormat('Y-m-d', '2019-01-01'), $cmd->options->get('start'));

        $this->assertEmpty($cmd->arguments);
    }
}