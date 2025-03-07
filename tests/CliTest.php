<?php

namespace TQ\Shamir\Tests;

use PHPUnit\Framework\TestCase;

class CliTest extends TestCase
{
    protected $secretUtf8 = 'Lorem ipsum dolor sit असरकारक संस्थान δισεντιας قبضتهم нолюёжжэ 問ナマ業71職げら覧品モス変害';

    protected $secretAscii;

    protected $descriptorSpec;

    protected $cmd;

    public function __construct($name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
        $this->cmd = __DIR__.'/../bin/shamir.php';
    }

    protected function setUp(): void
    {
        $this->descriptorSpec = [
            1 => ["pipe", "w"], // stdout is a pipe that the child will write to
            2 => ["pipe", "w"] // stderr is a pipe that the child will write to
        ];
    }

    protected function execute($cmd): array
    {
        $ret = [];

        $process = proc_open($cmd, $this->descriptorSpec, $pipes);
        if (is_resource($process)) {
            $ret['std'] = stream_get_contents($pipes[1]);
            fclose($pipes[1]);

            $ret['err'] = stream_get_contents($pipes[2]);
            fclose($pipes[2]);

            $ret['ret'] = proc_close($process);
        }

        return $ret;
    }

    public function provideUsage(): array
    {
        return [
            [$this->cmd, '.*Usage:.*'],
            [$this->cmd, '.*Available commands:.*'],
            [$this->cmd.' help', '.*Usage:.*'],
            [$this->cmd.' -h', '.*Usage:.*'],
            [$this->cmd.' --help', '.*Usage:.*'],
            [$this->cmd.' list', '.*Usage:.*'],
            [$this->cmd.' list', '.*Available commands:.*'],
            [$this->cmd.' list', '.*Available commands:.*'],
            [$this->cmd.' help shamir:share', '.*Create a shared secret.*'],
            [$this->cmd.' help shamir:recover', '.*Recover a shared secret.*'],
        ];
    }

    /**
     * @dataProvider provideUsage
     */
    public function testUsage($cmd, $regexp): void
    {
        $ret = $this->execute($cmd);

        self::assertEquals(0, $ret['ret']);
        self::assertMatchesRegularExpression('('.$regexp.')', $ret['std']);
        self::assertSame('', $ret['err']);
    }

    public function testWrongCommand(): void
    {
        $ret = $this->execute($this->cmd.' quatsch');

        self::assertEquals(1, $ret['ret']);
        self::assertSame('', $ret['std']);
        self::assertMatchesRegularExpression('(.*Command "quatsch" is not defined..*)', $ret['err']);
    }

    public function testUsageQuiet(): void
    {
        $ret = $this->execute($this->cmd.' help -q');

        self::assertEquals(0, $ret['ret']);
        self::assertSame('', $ret['std']);
        self::assertSame('', $ret['err']);
    }

    public function testVersion(): void
    {
        $ret = $this->execute($this->cmd.' -V');

        self::assertEquals(0, $ret['ret']);
        self::assertMatchesRegularExpression('(Shamir\'s Shared Secret CLI.*)', $ret['std']);
    }

    public function testFileInput(): void
    {
        $ret = $this->execute($this->cmd.' shamir:share -f ' . __DIR__ . '/secret.txt');
        self::assertEquals(0, $ret['ret']);
        self::assertMatchesRegularExpression('(10201.*)', $ret['std']);
        self::assertMatchesRegularExpression('(10202.*)', $ret['std']);
        self::assertMatchesRegularExpression('(10203.*)', $ret['std']);
    }

    public function testStandardInput(): void
    {
        $ret = $this->execute('echo -n "Share my secret" | '.$this->cmd.' shamir:share');
        self::assertEquals(0, $ret['ret']);
        self::assertMatchesRegularExpression('(10201.*)', $ret['std']);
        self::assertMatchesRegularExpression('(10202.*)', $ret['std']);
        self::assertMatchesRegularExpression('(10203.*)', $ret['std']);
    }
}
