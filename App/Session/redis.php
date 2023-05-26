<?php

namespace App\Session;

use App\Config;
use App\Session\SessionAdapterInterface;
// use Predis\Client as RedisClient;

class Redis implements SessionAdapterInterface
{
    protected $redis;
    protected \Engine\Di $di;

    public function __construct(\Engine\Di $di)
    {

        $this->redis = new \Redis();
        $this->redis->connect(\App\Config::session_redis_host, \App\Config::session_redis_port);
        // $this->redis = new RedisClient([
        //     'schema' => $schema,
        //     'host' => $hostname,
        //     'port' => $port,
        //     'password' => $password,
        //     'persistent' => $persistent
        // ]);

        $this->di = $di;
    }

    public function read(string $session_id): array
    {
        $data = $this->redis->get($session_id);

        return $data ? json_decode($data, true) : [];
    }

    public function write(string $session_id, array $data): bool
    {
        return $this->redis->set($session_id, json_encode($data), \App\Config::session_expire);
    }

    public function gc(string $max_lifetime): bool
    {
        // Redis automatically expires keys, so we don't need to do anything here.
        return true;
    }

    public function destroy(string $session_id): bool
    {
        return $this->redis->del($session_id);
    }
}
