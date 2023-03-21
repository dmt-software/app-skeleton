<?php

declare(strict_types=1);

namespace DMT\Config\Loaders;

use Closure;
use InvalidArgumentException;

class FileLoader implements FileLoaderInterface
{
    private readonly Closure $callback;

    public function __construct(private readonly string $extension = 'php', Closure $callback = null)
    {
        $this->callback = $callback ?? $this->callable(...);
    }

    /**
     * @inheritDoc
     */
    public function load(string $filename): ?array
    {
        if (!pathinfo(path: $filename, flags: PATHINFO_EXTENSION) == $this->extension) {
            return null;
        }

        return call_user_func($this->callback, $filename);
    }

    /**
     * Load function to use as default callback.
     *
     * @param string $file the file to read.
     *
     * @return array|null
     * @throws \InvalidArgumentException
     */
    private function callable(string $file): ?array
    {
        $config = @include $file;

        if ($config === false) {
            throw new InvalidArgumentException(message: 'could not file configuration file');
        }

        if ($config instanceof Closure) {
            return call_user_func(callback: $config);
        }

        return $config;
    }
}
