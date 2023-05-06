<?php

namespace Core;

use App\Config;

abstract class Model
{
    protected static function getDb()
    {
        static $db = null;
        if (!$db) {
            $dsn = 'mysql:host=' . Config::host . ';dbname=' . Config::dbname . ';charset=utf8';
            $db = new \PDO($dsn, Config::username, Config::password);
            $db->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        }
        return $db;
    }
}
