<?php

namespace DMT\Test\Config\Loaders;

use DMT\Config\Loaders\FileLoader;
use DMT\Config\Loaders\FileLoaderInterface;
use DMT\Config\Loaders\LoaderChain;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class LoaderChainTest extends TestCase
{
    public function testLoad(): void
    {
        $iniFileLoader = $this->getMockBuilder(FileLoaderInterface::class)
            ->onlyMethods(['load'])
            ->getMockForAbstractClass();

        $iniFileLoader
            ->expects($this->once())
            ->method('load')
            ->with($this->equalTo('config.ini'))
            ->willReturn($config = ['loaded' => true]);

        $notCalledFileLoader = $this->getMockBuilder(FileLoader::class)
            ->onlyMethods(['load'])
            ->getMock();

        $notCalledFileLoader
            ->expects($this->never())
            ->method('load');

        $loader = new LoaderChain([
            new FileLoader(),
            $iniFileLoader,
            $notCalledFileLoader,
        ]);

        $this->assertSame($config, $loader->load('config.ini'));
    }

    public function testNoConfigLoaded(): void
    {
        $this->expectExceptionObject(new InvalidArgumentException(message: 'no configuration found'));

        $loader = new LoaderChain([
            new FileLoader(),
            new FileLoader('ini', parse_ini_file(...))
        ]);

        $loader->load('config.json');
    }
}
