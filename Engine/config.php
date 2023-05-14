<?php

namespace Engine;

class Config
{
    protected string $directory;
    private $data = [];

    public function __construct()
    {
    }
    public function addPath(string $path)
    {
        $this->directory = $path;
    }
    public function get(string $key): mixed
    {
        return isset($this->data[$key]) ? $this->data[$key] : '';
    }

    public function set(string $key, mixed $data): void
    {
        $this->data[$key] = $data;
    }

    public function has(string $key): bool
    {
        return isset($this->data[$key]);
    }

    public function load(string $filename): array
    {
        $file = $this->directory . $filename . '.php';
        $namespace = '';

        if (is_file($file)) {
            $_config = [];
            require($file);
            $this->data = array_merge($this->data, $_config);
            return $this->data;
        }
        return [];
    }
}
