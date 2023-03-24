<?php

declare(strict_types=1);

namespace DMT\Test\Config\Loaders;

use DMT\Config\Loaders\FileLoader;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class FileLoaderTest extends TestCase
{
    public function testLoad(): void
    {
        $loader = new FileLoader('ini', parse_ini_file(...));

        $this->assertIsArray($loader->load(php_ini_loaded_file()));
    }

    public function testDefaultCallable(): void
    {
        $file = sys_get_temp_dir() . '/config.php';

        file_put_contents($file, '<?php return static function () { return []; };');

        try {
            $loader = new FileLoader();

            $this->assertIsArray($loader->load($file));
        } finally {
            unlink($file);
        }
    }

    public function testNotLoaded(): void
    {
        $loader = new FileLoader('ini', parse_ini_file(...));

        $this->assertNull($loader->load('some-file.php'));
    }

    public function testFileNotFound(): void
    {
        $this->expectExceptionObject(new InvalidArgumentException(message: 'configuration file not find'));

        (new FileLoader())->load('missing_file.php');
    }
}
