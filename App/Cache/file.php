<?php


namespace App\Cache;

/**
 * The File class implements the CacheInterface and provides caching functionality using files.
 */
class File implements CacheInterface
{
    private int $expire;
    private $storage = CACHE_FOLDER;

    /**
     * File constructor.
     *
     * @param \Engine\Di $di The dependency injection container.
     * @param int $expire The default expiration time for cached data in seconds.
     */
    public function __construct(\Engine\Di $di, int $expire = 3600)
    {
        $this->expire = $expire;
    }

    /**
     * Retrieves cached data based on the specified key.
     *
     * @param string $key The cache key.
     * @return array The cached data associated with the key.
     */
    public function get(string $key): array
    {
        $files = glob($this->storage . "cache.$key.*");

        if ($files) {
            return json_decode(file_get_contents($files[0]), true);
        }
        return [];
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
        $this->delete($key);
        $expire = $expire ?? $this->expire;
        $time = time() + $expire;
        file_put_contents($this->storage . "cache.$key.$time", json_encode($data));
    }

    /**
     * Deletes cached data based on the specified key.
     *
     * @param string $key The cache key.
     */
    public function delete(string $key): void
    {
        $files = glob($this->storage . "cache.$key.*");

        if ($files) {
            foreach ($files as $file) {
                if (!@unlink($file)) {
                    clearstatcache(false, $file);
                }
            }
        }
    }

    /**
     * Destructor to automatically clean up expired cache files.
     */
    public function __destruct()
    {
        $files = glob($this->storage . 'cache.*');

        if ($files && mt_rand(1, 100) == 1) {
            foreach ($files as $file) {
                $time = substr(strrchr($file, '.'), 1);
                if (time() > $time) {
                    if (!@unlink($file)) {
                        clearstatcache(false, $file);
                    }
                }
            }
        }
    }
}
