<?php

namespace App\DB;

use Core\Model;
use PDOException;

/**
 * The PDO class provides a wrapper around the PDO database connection and query execution.
 */
class PDO extends Db
{
    private \PDO $pdo;
    private QueryResult $queryResult;

    /**
     * PDO constructor.
     *
     * @param string|null $hostname The database hostname.
     * @param string|null $username The database username.
     * @param string|null $password The database password.
     * @param string $dbname The database name.
     * @param int $port The database port.
     * @param array $options Additional PDO options.
     * @throws ConnectionException If the connection to the database fails.
     */
    public function __construct(string $hostname = null, string $username = null, string $password = null, $dbname, $port = 3306, array $options = [])
    {
        $dsn = "mysql:host=$hostname;dbname=$dbname;charset=utf8mb4";
        try {
            $this->pdo = new \PDO($dsn, $username, $password, $options);
            $this->pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
            $this->pdo->setAttribute(\PDO::ATTR_EMULATE_PREPARES, false);
        } catch (PDOException $e) {
            throw new ConnectionException("Failed to connect to the database: {$e->getMessage()}", $e->getCode(), $e);
        }
    }

    /**
     * Executes a SQL query and returns the result.
     *
     * @param string $sql The SQL query.
     * @param array $params The query parameters.
     * @param string $class The class name to instantiate the result objects.
     * @return QueryResult The query result.
     * @throws QueryException If the query execution fails.
     */
    public function runQuery(string $sql, array $params = [], string $class = \stdClass::class): QueryResult
    {
        try {
            $stmt = $this->prepare($sql);
            $stmt->execute($params);
            $stmt->setFetchMode(\PDO::FETCH_CLASS | \PDO::FETCH_PROPS_LATE, $class);
            $rows = $stmt->fetchAll();
            $count = $stmt->rowCount();
            $lastInsertId = $this->pdo->lastInsertId();
            $this->queryResult = new QueryResult($rows, $count, $lastInsertId);

            return $this->queryResult;
        } catch (PDOException $e) {
            throw new QueryException($e->getMessage());
        }
    }

    /**
     * Prepares an SQL statement for execution.
     *
     * @param string $sql The SQL statement.
     * @return \PDOStatement The prepared statement.
     * @throws QueryException If the statement preparation fails.
     */
    public function prepare(string $sql): \PDOStatement
    {
        try {
            return $this->pdo->prepare($sql);
        } catch (PDOException $e) {
            throw new QueryException("Failed to prepare the query: {$e->getMessage()}", $e->getCode(), $e);
        }
    }

    /**
     * Returns the ID of the last inserted row or sequence value.
     *
     * @param string|null $name The name of the sequence object from which the ID should be returned.
     * @return string The last insert ID.
     */
    public function getLastInsertId(string $name = null): string
    {
        return $this->pdo->lastInsertId($name);
    }

    /**
     * Returns the number of rows affected by the last executed query.
     *
     * @return int The number of affected rows.
     */
    public function countAffected(): int
    {
        return $this->queryResult->getCount();
    }

    /**
     * Checks if the database connection is active.
     *
     * @return bool True if the connection is active, false otherwise.
     */
    public function isConnected(): bool
    {
        // Check if the PDO connection is active
        $connectionStatus = $this->pdo->getAttribute(\PDO::ATTR_CONNECTION_STATUS);

        if ($connectionStatus === 'Connected') {
            return true;
        }

        return false;
    }
}

/**
 * The QueryResult class represents the result of a database query.
 */
class QueryResult
{
    private array $rows;
    private int $count;
    private string $lastInsertId;

    /**
     * QueryResult constructor.
     *
     * @param array $rows The query result rows.
     * @param int $count The number of rows affected by the query.
     * @param string $lastInsertId The ID of the last inserted row or sequence value.
     */
    public function __construct(array $rows, int $count, string $lastInsertId)
    {
        $this->rows = $rows;
        $this->count = $count;
        $this->lastInsertId = $lastInsertId;
    }

    /**
     * Get the rows of the query result.
     *
     * @return array The query result rows.
     */
    public function getRows(): array
    {
        return $this->rows;
    }

    /**
     * Get the number of rows affected by the query.
     *
     * @return int The number of rows affected.
     */
    public function getCount(): int
    {
        return $this->count;
    }

    /**
     * Get the ID of the last inserted row or sequence value.
     *
     * @return string The last insert ID.
     */
    public function getLastInsertId(): string
    {
        return $this->lastInsertId;
    }
}

/**
 * The QueryException class represents an exception that occurs during a database query.
 */
class QueryException extends \RuntimeException
{
}

/**
 * The ConnectionException class represents an exception that occurs when connecting to the database fails.
 */
class ConnectionException extends \RuntimeException
{
}
