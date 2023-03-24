<?php

declare(strict_types=1);

namespace DMT\Config;

use DMT\Config\Loaders\FileLoader;
use DMT\Config\Loaders\FileLoaderInterface;
use InvalidArgumentException;

class Config
{
    private FileLoaderInterface $fileLoader;
    private array $options = [];

    public function __construct(FileLoaderInterface $fileLoader = null, array $options = [])
    {
        $this->fileLoader = $fileLoader ?? new FileLoader();
        $this->set(value: $options);
    }

    /**
     * Load configuration from a file.
     *
     * @param string $filename the config file to load.
     */
    public function load(string $filename): void
    {
        $this->set(value: $this->fileLoader->load(filename: $filename));
    }

    /**
     * Get a config option.
     *
     * @param string     $option  the (dotted) option to lookup, when omitted all the options are returned.
     * @param mixed|null $default the default value to return in case the option is not set.
     *
     * @return mixed
     */
    public function get(string $option = '', mixed $default = null): mixed
    {
        $options = $this->options;
        $keys = preg_split(pattern: '~(?<!\\\)\.~', subject: $option, flags: PREG_SPLIT_NO_EMPTY);
        foreach ($keys as $key) {
            if (is_null(value: $options[$key] ?? null)) {
                return $default;
            }

            $options = $options[$key];
        }

        return $options;
    }

    /**
     * Set an option in config.
     *
     * @param string|null $option the (dotted) option to store.
     * @param mixed|null  $value  the value to store in config.
     *
     * @return void
     * @throws \InvalidArgumentException when the configuration can not be stored.
     */
    public function set(string|null $option = null, mixed $value = null): void
    {
        if (!is_null(value: $option)) {
            $value = [$option => $value];
        }

        if (!is_array(value: $value) || array_filter(array: $value, callback: 'is_int', mode: ARRAY_FILTER_USE_KEY)) {
            throw new InvalidArgumentException(message: 'invalid configuration');
        }

        $value = $this->normalize(value: $value);

        array_walk(
            array: $value,
            callback: function (mixed $value, string $option) {
                $this->options = array_replace_recursive($this->options, [$option => $value]);
            }
        );
    }

    private function normalize(mixed $value): mixed
    {
        if (is_object(value: $value) || is_resource(value: $value)) {
            throw new InvalidArgumentException(message: 'invalid configuration');
        }
        if (!is_array(value: $value)) {
            return $value;
        }

        $return = [];
        foreach ($value as $key => $val) {
            $val = $this->normalize(value: $val);

            if (is_string(value: $key) && strpos(haystack: $key, needle: '.')) {
                $keys = array_reverse(preg_split(pattern: '~(?<!\\\)\.~', subject: $key, flags: PREG_SPLIT_NO_EMPTY));
                $key = array_pop(array: $keys);
                foreach ($keys as $k) {
                    $val = [$k => $val];
                };
            }

            $return = array_replace_recursive($return, [$key => $val]);
        }

        return $return;
    }
}
