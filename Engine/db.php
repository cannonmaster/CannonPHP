<?php

namespace Engine;

use \App\Config;
use Exception;

class Db
{
    protected $adapter;
    public function __construct($di, string $db_engine = Config::db_engine, string $hostname = Config::host, string $username = Config::username, string $password = Config::password, string $dbname = Config::dbname, int $port = Config::port)
    {
        $class = "App\\DB\\$db_engine";
        if (class_exists($class)) {
            $this->adapter = new $class($di, $hostname, $username, $password, $dbname, $port);
        } else {
            throw new \Exception("Error: could not load database adapter " . $db_engine);
        }
    }
    public function query(string $query)
    {
        return $this->adapter->query($query);
    }

    public function escape(string $value)
    {
        return $this->adapter->escape($value);
    }

    public function countAffected(): int
    {
        return $this->adapter->countAffected();
    }

    public function getLastId(): int
    {
        return $this->adapter->getLastId();
    }

    public function isConnected(): bool
    {
        return $this->adapter->isConnected();
    }
}
