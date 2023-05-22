<?php

namespace Engine;

class Cache
{
    /**
     * @var mixed Cache adapter instance
     */
    protected $adapter;

    /**
     * @var array Cache data array
     */
    protected array $data = [];

    /**
     * Cache constructor.
     *
     * @param string $cache_engine Cache engine name
     * @param \Engine\Di $di Dependency Injection container
     * @throws \Exception If cache engine is not found
     */
    public function __construct(string $cache_engine, \Engine\Di $di)
    {
        $class = "App\\Cache\\" . $cache_engine;
        if (class_exists($class)) {
            $this->adapter = new $class($di, \App\Config::cache_expire);
        } else {
            throw new \Exception("Error: cache engine not found");
        }
    }

    /**
     * Get cached data by key.
     *
     * @param string $key The cache key
     * @return mixed|null The cached data or null if not found
     */
    public function get(string $key)
    {
        return $this->adapter->get($key);
    }

    /**
     * Set cache data with key.
     *
     * @param string $key The cache key
     * @param mixed $data The data to be cached
     * @param int $expire Cache expiration time in seconds (optional)
     * @return void
     */
    public function set(string $key, $data, int $expire = \App\Config::cache_expire): void
    {
        $this->adapter->set($key, $data, $expire);
    }

    /**
     * Delete cached data by key.
     *
     * @param string $key The cache key
     * @return void
     */
    public function delete(string $key): void
    {
        $this->adapter->delete($key);
    }
}
