<?php

declare(strict_types=1);

namespace DMT\Test\Config;

use DMT\Config\Config;
use DMT\Config\Loaders\FileLoader;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class ConfigTest extends TestCase
{
    private const TEST_CONFIG = [
        'a' => [
            "b" => [
                "c" => "text",
                "e" => 0
            ]
        ],
        'f' => true,
        'h' => [
            "i" => 3.14159265359
        ],
        'j' => ["k", "l", "m"],
    ];

    public function testLoad(): void
    {
        $fh = tmpfile();
        fwrite($fh, sprintf('<?php return %s;', var_export(self::TEST_CONFIG, true)));

        try {
            $config = new Config(fileLoader: new FileLoader(extension: ''));
            $config->load(stream_get_meta_data($fh)['uri']);

            $this->assertSame(self::TEST_CONFIG, $config->get());
        } finally {
            fclose($fh);
        }
    }

    /**
     * @dataProvider configSetProvider
     */
    public function testSet(string $option = null, mixed $value = null, mixed $expected = null): void
    {
        $config = new Config(options: self::TEST_CONFIG);
        $config->set(option: $option, value: $value);

        $this->assertSame($expected, $config->get($option, $value));
    }

    public static function configSetProvider(): iterable
    {
        return [
            'set option' => ['g', 'isset', 'isset'],
            'set dotted option' => ['a.b.d', true, true],
            'replace option in array ' => ['a', ['b' => ['e' => 123]], ['b' => ['c' => 'text', 'e' => 123]]],
            'override option' => ['a.b.c', 'new text', 'new text'],
            'append existing option in array' => ['a.b', ['d' => 2.0], ["c" => "text", "e" => 0, 'd' => 2.0]],
            'override existing array keys' => ['j', ["o", "n"], ["o", "n", "m"]],
            'empty options' => ['h', null, null],
            'empty dotted option' => ['a.b.c', null, null],
        ];
    }

    /**
     * @dataProvider configGetProvider
     */
    public function testGet(string $option = '', mixed $default = null, mixed $expected = null): void
    {
        $config = new Config(options: self::TEST_CONFIG);

        $this->assertSame($expected, $config->get($option, $default));
    }

    public static function configGetProvider(): iterable
    {
        return [
            'retrieve all options' => ['', null, self::TEST_CONFIG],
            'get subset' => ['a', null, self::TEST_CONFIG['a']],
            'get subset with dotted slug' => ['a.b', null, self::TEST_CONFIG['a']['b']],
            'get string' => ['a.b.c', null, self::TEST_CONFIG['a']['b']['c']],
            'get int' => ['a.b.e', null, self::TEST_CONFIG['a']['b']['e']],
            'get pi' => ['h.i', 'pi', self::TEST_CONFIG['h']['i']],
            'missing options' => ['a.b.d', null, null],
            'missing option with default' => ['g', 'default', 'default'],
        ];
    }

    /**
     * @dataProvider invalidConfigProvider
     */
    public function testConfigFailure(string $key = null, mixed $value = null)
    {
        $this->expectExceptionObject(new InvalidArgumentException(message: 'invalid configuration'));

        $config = new Config();
        $config->set(option: $key, value: $value);
    }

    public static function invalidConfigProvider(): iterable
    {
        return [
            'fail: empty config' => [null, null],
            'fail: scalar value without key' => [null, 'value'],
            'fail: object in config' => ['option', new \stdClass()],
            'fail: resource in config' => ['option', stream_context_create()],
        ];
    }
}
