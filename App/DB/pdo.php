<?php

namespace App\DB;

use PDOException;

class PDO
{
    private \PDO $pdo;

    public function __construct($di, string $hostname = null, string $username = null, string $password = null, $dbname, $port = 3306, array $options = [])
    {
        $dsn = "mysql:host=$hostname;dbname=$dbname;charset=utf8mb4";
        try {
            $this->pdo = new \PDO($dsn, $username, $password, $options);
            $this->pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
            $this->pdo->setAttribute(\PDO::ATTR_EMULATE_PREPARES, false);
        } catch (PDOException $e) {
            throw new ConnectionException("Failed to connect to database: {$e->getMessage()}", $e->getCode(), $e);
        }
    }

    public function query(string $sql, array $params = []): QueryResult
    {
        $stmt = $this->prepare($sql);
        $stmt->execute($params);

        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        $count = $stmt->rowCount();
        $lastInsertId = $this->pdo->lastInsertId();

        return new QueryResult($rows, $count, $lastInsertId);
    }

    public function prepare(string $sql): \PDOStatement
    {
        try {
            return $this->pdo->prepare($sql);
        } catch (PDOException $e) {
            throw new QueryException("Failed to prepare query: {$e->getMessage()}", $e->getCode(), $e);
        }
    }

    public function getLastInsertId(string $name = null): string
    {
        return $this->pdo->lastInsertId($name);
    }
}

class QueryResult
{
    private array $rows;
    private int $count;
    private string $lastInsertId;

    public function __construct(array $rows, int $count, string $lastInsertId)
    {
        $this->rows = $rows;
        $this->count = $count;
        $this->lastInsertId = $lastInsertId;
    }

    public function getRows(): array
    {
        return $this->rows;
    }

    public function getCount(): int
    {
        return $this->count;
    }

    public function getLastInsertId(): string
    {
        return $this->lastInsertId;
    }
}

class QueryException extends \RuntimeException
{
}
class ConnectionException extends \RuntimeException
{
}
