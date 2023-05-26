<?php

namespace Core;

use App\Config;

abstract class Model
{
    /**
     * Get the database connection instance.
     *
     * @return \PDO The database connection instance.
     */
    protected static function getDb(
        string $db_engine = Config::db_engine,
        string $hostname = Config::host,
        string $username = Config::username,
        string $password = Config::password,
        string $dbname = Config::dbname,
        int $port = Config::port
    ) {
        static $db = null;
        if (!$db) {
            $class = "App\\DB\\$db_engine";
            if (class_exists($class)) {
                $db = new $class($hostname, $username, $password, $dbname, $port);
            } else {
                throw new \Exception("Error: could not load database adapter " . $db_engine);
            }
            // $dsn = 'mysql:host=' . Config::host . ';dbname=' . Config::dbname . ';charset=utf8';
            // $db = new \PDO($dsn, Config::username, Config::password);
            // $db->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        }
        return $db;
    }
}
