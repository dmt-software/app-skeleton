<?php

declare(strict_types=1);

namespace DMT\Config\Loaders;

interface FileLoaderInterface
{
    /**
     * Load the configuration into an array.
     *
     * @param string $filename the configuration file to load.
     *
     * @return array|null
     * @throws \InvalidArgumentException
     */
    public function load(string $filename): ?array;
}
