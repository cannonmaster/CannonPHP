<?php

namespace Engine;

class Cache
{
    protected $adapter;
    protected array $data = [];
    public function __construct($cache_engine, $di)
    {
        $class = "App\\Cache\\" . $cache_engine;
        if (class_exists($class)) {
            $this->adapter = new $class($di);
        } else {
            throw new \Exception("Error: cache not found");
        }
    }

    public function get($key)
    {
        return $this->adapter->get($key);
    }

    public function set($key, $data)
    {
        return $this->adapter->set($key, $data);
    }

    public function delete($key)
    {
        return $this->adapter->delete($key);
    }
}
