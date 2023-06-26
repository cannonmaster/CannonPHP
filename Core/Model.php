<?php

namespace Core;

use App\Config;
use App\DB\CommandBuilderInterface;

abstract class Model
{
    private static $db;
    private static $command_builder;

    /**
     * Get the database connection instance.
     *
     * @param string $db_engine Database engine to use.
     * @param string $hostname  Hostname of the database server.
     * @param string $username  Username for the database connection.
     * @param string $password  Password for the database connection.
     * @param string $dbname    Name of the database to connect to.
     * @param int    $port      Port number for the database connection.
     *
     * @return \App\DB\PDO|object The database connection instance.
     * @throws \Exception If the database adapter class is not found.
     */
    public static function getDb(
        string $db_engine = Config::db_engine,
        string $hostname = Config::host,
        string $username = Config::username,
        string $password = Config::password,
        string $dbname = Config::dbname,
        int $port = Config::port
    ) {
        if (!self::$db) {
            $class = "App\\DB\\$db_engine";
            if (class_exists($class)) {
                self::$db = new $class($hostname, $username, $password, $dbname, $port);
            } else {
                throw new \Exception("Error: could not load database adapter " . $db_engine);
            }
        }
        return self::$db;
    }

    /**
     * Get the command builder instance.
     *
     * @param CommandBuilderInterface|null $command_builder The command builder instance.
     *
     * @return CommandBuilderInterface The command builder instance.
     * @throws \Exception If the command builder class does not exist.
     */
    public static function getCommandBuilder(CommandBuilderInterface $command_builder = null)
    {
        if ($command_builder === null) {
            $command_builder = \App\Config::db_command_builder;
        }
        $command_builder = "App\\DB\\$command_builder";
        if (class_exists($command_builder)) {
            $command_builder = new $command_builder();
            return $command_builder;
        } else {
            throw new \Exception("Error: the command builder class $command_builder does not exist");
        }
    }
}
