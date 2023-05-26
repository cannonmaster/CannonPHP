<?php

namespace App\Session;

use App\Session\SessionAdapterInterface;
use PDO;

class Db implements SessionAdapterInterface
{
    protected $db;
    protected $table;
    protected $di;

    public function __construct(\Engine\Di $di)
    {
        if (!$di->has('db')) {
            $di->set('db', new \Engine\Db($di));
        }
        $this->db = $di->get('db');
        $this->table = \App\Config::session_db_table;
        $this->di = $di;
    }

    public function read(string $session_id): array
    {
        $stmt = $this->db->prepare("SELECT data from {$this->table} WHERE session_id = ?");
        $stmt->execute([$session_id]);
        $data = $stmt->fetch(PDO::FETCH_COLUMN);
        return $data ? unserialize($data) : [];
    }

    public function write(string $session_id, array $data): bool
    {
        $serializedData = serialize($data);
        try {
            $stmt = $this->db->prepare("REPLACE INTO {$this->table} (session_id, data) VALUES (?, ?)");
            return $stmt->execute([$session_id, $serializedData]);
        } catch (\PDOException $e) {
            // handle error 
            return false;
        }
    }

    public function destroy(string $session_id): bool
    {
        try {

            $stmt = $this->db->prepare("DELETE FROM {$this->table} WHERE session_id = ?");
            return $stmt->execute([$session_id]);
        } catch (\PDOException $e) {
            return false;
        }
    }

    public function gc(string $lifetime): bool
    {
        $maxLifetime = time() - $lifetime;
        try {
            $stmt = $this->db->prepare("DELETE FROM {$this->table} WHERE last_activity < ?");
            $stmt->execute([$maxLifetime]);
        } catch (\PDOException $e) {
            return false;
        }
    }
}
