<?php

declare(strict_types=1);

namespace DMT\Config\Loaders;

use InvalidArgumentException;

class LoaderChain implements FileLoaderInterface
{
    /**
     * @var array<FileLoaderInterface>
     */
    private array $loaders = [];

    public function __construct(array $loaders)
    {
        array_map($this->addLoader(...), $loaders);
    }

    /**
     * Add a loader to the chain.
     *
     * @param FileLoaderInterface $loader
     */
    public function addLoader(FileLoaderInterface $loader): void
    {
        $this->loaders[] = $loader;
    }

    /**
     * @inheritDoc
     */
    public function load(string $filename): ?array
    {
        foreach ($this->loaders as $loader) {
            if ($config = $loader->load(filename: $filename)) {
                return $config;
            }
        }

        throw new InvalidArgumentException(message: 'no configuration found');
    }
}
