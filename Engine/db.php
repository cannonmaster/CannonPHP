<?php

namespace Engine;

use App\Config;
use Core\Model;
use Exception;

class Db extends Model
{
    /**
     * @var mixed Database adapter instance
     */
    protected $adapter;

    /**
     * Db constructor.
     *
     * @param mixed $di Dependency Injection container
     * @param string $db_engine The database engine
     * @param string $hostname The database hostname
     * @param string $username The database username
     * @param string $password The database password
     * @param string $dbname The database name
     * @param int $port The database port
     * @throws Exception If the database adapter class does not exist
     */
    public function __construct(
        \Engine\Di $di,
        string $db_engine = Config::db_engine,
        string $hostname = Config::host,
        string $username = Config::username,
        string $password = Config::password,
        string $dbname = Config::dbname,
        int $port = Config::port
    ) {
        $this->adapter = static::getDb($db_engine, $hostname, $username, $password, $dbname, $port);
        // $class = "App\\DB\\$db_engine";
        // if (class_exists($class)) {
        //     $this->adapter = new $class($di, $hostname, $username, $password, $dbname, $port);
        // } else {
        //     throw new Exception("Error: could not load database adapter " . $db_engine);
        // }
    }

    /**
     * Execute a database query.
     *
     * @param string $query The SQL query to execute
     * @return mixed The query result
     */
    public function query(string $query): mixed
    {
        return $this->adapter->query($query);
    }

    /**
     * Escape a string for use in a database query.
     *
     * @param string $value The value to escape
     * @return string The escaped value
     */
    public function escape(string $value): string
    {
        return $this->adapter->escape($value);
    }

    /**
     * Get the number of rows affected by the last query.
     *
     * @return int The number of affected rows
     */
    public function countAffected(): int
    {
        return $this->adapter->countAffected();
    }

    /**
     * Get the last inserted ID.
     *
     * @return int The last inserted ID
     */
    public function getLastId(): int
    {
        return $this->adapter->getLastId();
    }

    /**
     * Check if the database connection is active.
     *
     * @return bool True if connected, false otherwise
     */
    public function isConnected(): bool
    {
        return $this->adapter->isConnected();
    }
}
