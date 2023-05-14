<?php

namespace Engine;

class Di
{
    private $data = [];

    public function __get(string $key)
    {
        return $this->get($key);
    }
    public function __set(string $key, object $value): void
    {
        $this->set($key, $value);
    }
    public function set(string $key, object $value): void
    {
        $this->data[$key] = $value;
    }
    public function get(string $key)
    {
        return isset($this->data[$key]) ? $this->data[$key] : null;
    }
    public function has(string $key): bool
    {
        return isset($this->data[$key]);
    }
    public function unset(string $key): void
    {
        if (isset($this->data[$key])) {
            unset($this->data[$key]);
        }
    }
}
