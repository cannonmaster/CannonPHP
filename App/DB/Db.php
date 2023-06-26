<?php

namespace App\DB;

use App\DB\ORM\Builder;

/**
 * Class Db
 *
 * @package App\DB
 */
abstract class Db
{
  /**
   * Run a SQL query and retrieve the results.
   *
   * @param string $sql    The SQL query to execute.
   * @param array  $params The parameters to bind to the query.
   * @param string $class  The class to instantiate for each result row.
   * @return mixed The query result.
   */
  public abstract function runQuery(string $sql, array $params, string $class);

  /**
   * Get the ID of the last inserted row.
   *
   * @param string $name The name of the ID sequence or generator.
   * @return string The last insert ID.
   */
  public abstract function getLastInsertId(string $name);

  /**
   * Check if the database connection is established.
   *
   * @return bool Whether the database is connected.
   */
  public abstract function isConnected(): bool;

  /**
   * Get the number of affected rows from the last query.
   *
   * @return int The number of affected rows.
   */
  public abstract function countAffected();

  /**
   * Get the command builder instance.
   *
   * @return CommandBuilderInterface The command builder instance.
   * @throws \Exception If the command builder adapter cannot be loaded.
   */
  public static function getCommandBuilder(): CommandBuilderInterface
  {
    static $command_builder = null;
    if (!$command_builder) {
      $cb = \App\Config::db_command_builder;
      $class_name = "App\\DB\\$cb";
      if (class_exists($class_name)) {
        $command_builder = new $class_name();
      } else {
        throw new \Exception("Error: could not load command builder adapter");
      }
    }
    return $command_builder;
  }
}
