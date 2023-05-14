<?php

namespace Engine;

class Language
{
    protected string $code;
    protected string $directory;
    protected array $path = [];
    protected array $data = [];
    protected array $cache = [];

    public function __construct(string $code, string $directory)
    {
        $this->code = $code;
        $this->directory = $directory;
    }

    public function get($key)
    {
        return $this->data[$key] ?? null;
    }

    public function set($key, $data)
    {
        $this->data[$key] = $data;
    }

    public function load(string $filename, string $code = '')
    {
        $code = $code ?: $this->code;

        if (!isset($this->cache[$code][$filename])) {

            $_ = [];
            $file = realpath($this->directory . $code . '/' . $filename . '.php');

            if ($file && is_file($file)) {

                require($file);
                $this->cache[$code][$filename] = $_;
            } else {
                throw new \Exception('Language file not found: ' . $filename);
            }
        } else {
            $_ = $this->cache[$code][$filename];
        }
        $this->data = array_merge($this->data, $_);

        return $this->data;
    }
}
