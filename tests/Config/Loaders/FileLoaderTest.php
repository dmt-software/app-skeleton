<?php

declare(strict_types=1);

namespace DMT\Test\Config\Loaders;

use DMT\Config\Loaders\FileLoader;
use PHPUnit\Framework\TestCase;

class FileLoaderTest extends TestCase
{
    public function testLoad(): void
    {
        $loader = new FileLoader('ini', parse_ini_file(...));

        $this->assertIsArray($loader->load(php_ini_loaded_file()));
    }

    public function testNotLoaded(): void
    {
        $loader = new FileLoader('ini', parse_ini_file(...));

        $this->assertNull($loader->load('some-file.php'));
    }
}
