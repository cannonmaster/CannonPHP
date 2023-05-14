<?php

namespace App\Cache;

// use Predis\Client as RedisClient;
use App\Config;

class Redis
{
    protected $redis;
    public function  __construct(\Engine\Di $di, string $hostname = Config::cache_hostname, string $schema = Config::cache_schema, string $password = Config::cache_password, string $persistent = Config::cache_persistent, int $port = Config::cache_port)
    {
        $this->redis = new \Redis();
        $this->redis->connect($hostname, $port);
        // $this->client = new RedisClient([
        //     'schema' => $schema,
        //     'host' => $hostname,
        //     'port' => $port,
        //     'password' => $password,
        //     'persistent' => $persistent
        // ]);
    }

    public function set($key, $data)
    {
        $this->redis->set($key, json_encode($data));
    }

    public function get($key)
    {
        $data = $this->redis->get($key);
        return $data ? json_decode($data, true) : [];
    }

    public function delete($key)
    {
        $this->redis->del($key);
    }
}
