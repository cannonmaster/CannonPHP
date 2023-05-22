<?php

namespace App\Cache;

/**
 * The Redis class implements the CacheInterface and provides caching functionality using Redis.
 */
class Redis implements CacheInterface
{
    protected $redis;
    protected $expire;

    /**
     * Redis constructor.
     *
     * @param \Engine\Di $di The dependency injection container.
     * @param int $expire The default expiration time for cached data in seconds.
     */
    public function  __construct(\Engine\Di $di, int $expire = 3600)
    {
        $this->expire = $expire;
        $this->redis = new \Redis();
        $this->redis->connect(\App\Config::cache_hostname, \App\Config::cache_port);

        // Check if password is required
        $requiresAuth = $this->redis->config('GET', 'requirepass');
        if (isset($requiresAuth['requirepass']) && !empty($requireAuth['requirepass'])) {
            // Password is required, authenticate
            $this->redis->auth(\App\Config::cache_password);
        }
    }

    /**
     * Stores data in the cache with the specified key and optional expiration time.
     *
     * @param string $key The cache key.
     * @param mixed $data The data to be stored in the cache.
     * @param int|null $expire The expiration time for the cached data in seconds. If null, the default expiration time should be used.
     */
    public function set(string $key, mixed $data, int $expire = null): void
    {
        $expire = $expire ?? $this->expire;
        $res = $this->redis->set($key, json_encode($data));
        if ($res) {
            $this->redis->expire($key, $expire);
        }
    }

    /**
     * Retrieves cached data based on the specified key.
     *
     * @param string $key The cache key.
     * @return array The cached data associated with the key.
     */
    public function get(string $key): array
    {
        $data = $this->redis->get($key);
        return $data ? json_decode($data, true) : [];
    }

    /**
     * Deletes cached data based on the specified key.
     *
     * @param string $key The cache key.
     */
    public function delete(string $key): void
    {
        $this->redis->del($key);
    }
}
