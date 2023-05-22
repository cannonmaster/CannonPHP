<?php

namespace Engine;

class Config
{
    /**
     * @var string Configuration directory path
     */
    protected string $directory;

    /**
     * @var array Configuration data array
     */
    private array $data = [];

    /**
     * Config constructor.
     */
    public function __construct()
    {
    }

    /**
     * Add configuration directory path.
     *
     * @param string $path The directory path
     * @return void
     */
    public function addPath(string $path): void
    {
        $this->directory = $path;
    }

    /**
     * Get configuration value by key.
     *
     * @param string $key The configuration key
     * @return mixed The configuration value
     */
    public function get(string $key): mixed
    {
        return isset($this->data[$key]) ? $this->data[$key] : '';
    }

    /**
     * Set configuration value by key.
     *
     * @param string $key The configuration key
     * @param mixed $data The configuration value
     * @return void
     */
    public function set(string $key, mixed $data): void
    {
        $this->data[$key] = $data;
    }

    /**
     * Check if configuration key exists.
     *
     * @param string $key The configuration key
     * @return bool True if key exists, false otherwise
     */
    public function has(string $key): bool
    {
        return isset($this->data[$key]);
    }

    /**
     * Load configuration from a file.
     *
     * @param string $filename The configuration file name
     * @return array The loaded configuration data
     */
    public function load(string $filename): array
    {
        $file = $this->directory . $filename . '.php';
        $namespace = '';

        if (is_file($file)) {
            $_config = [];
            require $file;
            $this->data = array_merge($this->data, $_config);
            return $this->data;
        }
        return [];
    }
}
