<?php

namespace App\Cache;

class File
{
    private int $expire;
    public $storage = '/App/';
    public function __construct(\Engine\Di $di, int $expire = 3600)
    {
        $this->expire = $expire;
    }

    public function  get(string $key)
    {
        $files = glob($this->storage . "cache.$key.*");

        if ($files) {
            return json_decode(file_get_contents($files[0]), true);
        }
        return [];
    }
    public function set(string $key, $data, int $expire = null): void
    {
        $this->delete($key);
        $expire = $expire ?? $this->expire;
        $time = time() + $expire;
        file_put_contents($this->storage . "cache.$key.$time", json_encode($data));
    }
    public function delete(string $key)
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
