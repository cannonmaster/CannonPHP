<?php

namespace Engine;

use App\Config;
use Exception;
use Core\Model as DbConnection;

class Db
{
    /**
     * @var mixed Database adapter instance
     */
    protected $adapter;

    /**
     * Db constructor.
     *
     * @param \Engine\Di $di         Dependency Injection container.
     * @param string     $db_engine  The database engine.
     * @param string     $hostname   The database hostname.
     * @param string     $username   The database username.
     * @param string     $password   The database password.
     * @param string     $dbname     The database name.
     * @param int        $port       The database port.
     *
     * @throws Exception If the database adapter class does not exist.
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
        $this->adapter = DbConnection::getDb($db_engine, $hostname, $username, $password, $dbname, $port);
    }

    /**
     * Execute a database query.
     *
     * @param string $query  The SQL query to execute.
     * @param array  $params The query parameters.
     *
     * @return mixed The query result.
     */
    public function runQuery(string $query, $params = []): mixed
    {
        return $this->adapter->runQuery($query, $params);
    }

    /**
     * Get the number of rows affected by the last query.
     *
     * @return int The number of affected rows.
     */
    public function countAffected(): int
    {
        return $this->adapter->countAffected();
    }

    /**
     * Get the last inserted ID.
     *
     * @return int The last inserted ID.
     */
    public function getLastInsertId(): int
    {
        return $this->adapter->getLastInsertId();
    }

    /**
     * Check if the database connection is active.
     *
     * @return bool True if connected, false otherwise.
     */
    public function isConnected(): bool
    {
        return $this->adapter->isConnected();
    }
}
