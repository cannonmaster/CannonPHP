<?php

namespace App\Cache;

/**
 * The CacheInterface defines the methods that should be implemented by cache classes.
 */
interface CacheInterface
{
    /**
     * Retrieves data from the cache based on the specified key.
     *
     * @param string $key The cache key.
     * @return array The cached data associated with the key.
     */
    public function get(string $key): array;

    /**
     * Stores data in the cache with the specified key and optional expiration time.
     *
     * @param string $key The cache key.
     * @param mixed $data The data to be stored in the cache.
     * @param int|null $expire The expiration time for the cached data in seconds. If null, the default expiration time should be used.
     */
    public function set(string $key, mixed $data, int $expire = null): void;

    /**
     * Deletes data from the cache based on the specified key.
     *
     * @param string $key The cache key.
     */
    public function delete(string $key): void;
}
