<?php

namespace App\DB;

use App\DB\CommandBuilderInterface;
use App\DB\QueryResult;
use Core\Model as DbConnection;

class MysqlCommandBuilder extends \Engine\DB implements CommandBuilderInterface
{
  /**
   * @var string The table name.
   */
  private $table = '';

  /**
   * @var array The columns to select.
   */
  private $select = [];

  /**
   * @var string The WHERE clause.
   */
  private $where = '';

  /**
   * @var string The JOIN clause.
   */
  private $join = '';

  /**
   * @var string The ORDER BY clause.
   */
  private $orderBy = '';

  /**
   * @var string The GROUP BY clause.
   */
  private $groupBy = '';

  /**
   * @var string The HAVING clause.
   */
  private $having = '';

  /**
   * @var string The OFFSET clause.
   */
  private $offset = '';

  /**
   * @var string The LIMIT clause.
   */
  private $limit = '';

  /**
   * @var string The generated command string.
   */
  private $commandString = '';

  /**
   * @var array The clauses in the SELECT command.
   */
  private $select_command_clause = ['join', 'where', 'groupBy', 'having', 'orderBy', 'limit', 'offset'];

  /**
   * @var bool Whether to append an "AND" keyword.
   */
  private $append_and_keyword = false;

  /**
   * @var bool Whether to append an "OR" keyword.
   */
  private $append_or_keyword = false;

  /**
   * MysqlCommandBuilder constructor.
   */
  public function __construct()
  {
  }

  /**
   * Set the table name for the command.
   *
   * @param string $table The table name.
   * @return CommandBuilderInterface
   */
  public function table(string $table): CommandBuilderInterface
  {
    $this->table = $table;
    return $this;
  }

  /**
   * Set the columns to select.
   *
   * @param string|array $select The columns to select.
   * @return CommandBuilderInterface
   */
  public function select(string|array $select): CommandBuilderInterface
  {
    if (is_string($select)) {
      if (!in_array($select, $this->select)) {
        array_push($this->select, $select);
      }
    } else if (is_array($select)) {
      $this->select = $select;
    }

    return $this;
  }

  /**
   * Set the ORDER BY clause.
   *
   * @param string $col       The column to order by.
   * @param string $direction The sort direction.
   * @return CommandBuilderInterface
   */
  public function orderBy(string $col, string $direction = "ASC"): CommandBuilderInterface
  {
    $this->orderBy = "ORDER BY " . $col . " " . $direction;
    return $this;
  }

  /**
   * Set the LIMIT clause.
   *
   * @param int $limit The number of rows to limit.
   * @return CommandBuilderInterface
   */
  public function limit(int $limit): CommandBuilderInterface
  {
    $this->limit = 'LIMIT ' . $limit;
    return $this;
  }

  /**
   * Set the OFFSET clause.
   *
   * @param int $offset The number of rows to offset.
   * @return CommandBuilderInterface
   */
  public function offset(int $offset): CommandBuilderInterface
  {
    $this->offset = 'OFFSET ' . $offset;
    return $this;
  }

  /**
   * Set the WHERE clause.
   *
   * @param string       $col      The column name.
   * @param string|null  $operator The comparison operator.
   * @param mixed|null   $value    The value to compare.
   * @return CommandBuilderInterface
   */
  public function where(string $col, string $operator = null, mixed $value = null): CommandBuilderInterface
  {
    $args = func_get_args();

    if (empty($this->where)) {
      $this->where = ' WHERE ';
    }
    if (count($args) == 3) {
      if ($this->append_and_keyword) {
        $this->where .= ' AND ';
      }
      $this->where .= $col . " " . $operator . " '" . $value . "'";
      $this->append_and_keyword = true;
    } else if (count($args) === 1) {
      $this->where .= ' ( ';
      $this->append_and_keyword = false;
      $this->append_or_keyword = false;
      $col($this);
      $this->where .= ' ) ';
    }

    return $this;
  }

  /**
   * Set the OR WHERE clause.
   *
   * @param string       $col      The column name.
   * @param string|null  $operator The comparison operator.
   * @param mixed|null   $value    The value to compare.
   * @return CommandBuilderInterface
   */
  public function orWhere(string $col, string $operator = null, mixed $value = null): CommandBuilderInterface
  {
    $args = count(func_get_args());
    if (empty($this->where)) {
      $this->where = " WHERE ";
    }
    if ($args === 3) {
      if ($this->append_or_keyword)
        $this->where .= ' OR ';
      $this->where .= $col . " " . $operator . " '" . $value . "'";

      $this->append_or_keyword = true;
    } else if ($args === 1) {
      if ($this->where !== " WHERE ")
        $this->where .= " OR ";
      if ($this->append_or_keyword)
        $this->where .= " OR ";
      $this->where .= " ( ";
      $this->append_or_keyword = false;
      $this->append_and_keyword = false;
      $col($this);
      $this->where .= " ) ";
    }

    return $this;
  }

  /**
   * Set the WHERE IN clause.
   *
   * @param string $col    The column name.
   * @param array  $values The array of values.
   * @return CommandBuilderInterface
   */
  public function whereIn(string $col, array $values): CommandBuilderInterface
  {
    if (empty($this->where)) {
      $this->where = " WHERE ";
    }
    if ($this->append_and_keyword || $this->append_or_keyword) {
      $this->where .= ' AND ';
    }
    $this->where .= $col . " IN ('" . implode("','", $values) . "')";
    $this->append_and_keyword = true;
    return $this;
  }

  /**
   * Set the OR WHERE IN clause.
   *
   * @param string $col    The column name.
   * @param array  $values The array of values.
   * @return CommandBuilderInterface
   */
  public function orWhereIn(string $col, array $values): CommandBuilderInterface
  {
    if (empty($this->where)) {
      $this->where = ' WHERE ';
    }
    if ($this->append_and_keyword || $this->append_or_keyword) {
      $this->where .= " OR ";
    }

    $this->where .= $col . " IN ('" . implode("','", $values) . "')";
    $this->append_or_keyword = true;
    return $this;
  }

  /**
   * Build the SELECT command.
   *
   * @return CommandBuilderInterface
   */
  private function buildSelectCommand()
  {
    if (empty($this->limit) && !empty($this->offset)) {
      $this->limit(PHP_INT_MAX);
    }

    $command = '';
    if (count($this->select) === 0) {
      $command .= $this->table . ".*";
    } else {
      $command = implode(',', $this->select);
    }

    $command = "SELECT " . $command . " FROM " . $this->table . " ";

    foreach ($this->select_command_clause as $clause) {
      if (!empty($this->$clause)) {
        $command .= $this->$clause . ' ';
      }
    }

    $command = rtrim($command);
    $this->commandString = $command;
    return $this;
  }

  /**
   * Join a table.
   *
   * @param string $table    The table name.
   * @param string $col1     The column on the current table.
   * @param string $operator The join operator.
   * @param string $col2     The column on the joined table.
   * @return CommandBuilderInterface
   */
  public function join(string $table, string $col1, string $operator, string $col2): CommandBuilderInterface
  {
    $this->join .= " INNER JOIN " . $table . " ON " . $col1 . " " . $operator . " " . $col2;
    return $this;
  }

  /**
   * Execute the SELECT query and return the results.
   *
   * @param string $class The class name to map the results to.
   * @return QueryResult
   */
  public function get(string $class = \stdClass::class): QueryResult
  {
    $this->buildSelectCommand();
    $db = DbConnection::getDb();
    $data = $db->runQuery($this->commandString, [], $class);
    return $data;
  }

  /**
   * Calculate the average value of a column.
   *
   * @param string $col The column name.
   * @return mixed
   */
  public function avg(string $col): mixed
  {
    $select = ["AVG(" . $col . ")"];
    $this->select($select);
    $data = $this->get();
    $obj = current($data->getRows());
    $prop = current($select);
    return $obj->$prop;
  }

  /**
   * Calculate the sum of a column.
   *
   * @param string $col The column name.
   * @return mixed
   */
  public function sum(string $col): mixed
  {
    $select = ["SUM(" . $col . ")"];
    $this->select($select);
    $data = $this->get();
    $obj = current($data->getRows());
    $prop = current($select);
    return $obj->$prop;
  }

  /**
   * Count the number of rows.
   *
   * @param string $col The column name.
   * @return mixed
   */
  public function count(string $col): mixed
  {
    $select = ["COUNT(" . $col . ")"];
    $this->select($select);
    $data = $this->get();
    $obj = current($data->getRows());
    $prop = current($select);
    return $obj->$prop;
  }

  /**
   * Find the minimum value of a column.
   *
   * @param string $col The column name.
   * @return mixed
   */
  public function min(string $col): mixed
  {
    $select = ["MIN(" . $col . ")"];
    $this->select($select);
    $data = $this->get();
    $obj = current($data->getRows());
    $prop = current($select);
    return $obj->$prop;
  }

  /**
   * Find the maximum value of a column.
   *
   * @param string $col The column name.
   * @return mixed
   */
  public function max(string $col): mixed
  {
    $select = ["MAX(" . $col . ")"];
    $this->select($select);
    $data = $this->get();
    $obj = current($data->getRows());
    $prop = current($select);
    return $obj->$prop;
  }

  /**
   * Insert a new row into the table.
   *
   * @param array $data The data to be inserted.
   * @return int The number of affected rows.
   */
  public function insert(array $data): int
  {
    $this->buildInsertCommand($data);
    $db = DbConnection::getDb();
    return $db->runQuery($this->commandString)->getCount();
  }

  /**
   * Insert a new row into the table and return the last inserted ID.
   *
   * @param array $data The data to be inserted.
   * @return string The last inserted ID.
   */
  public function insertGetId(array $data): string
  {
    $this->buildInsertCommand($data);
    $db = DbConnection::getDb();
    return $db->runQuery($this->commandString)->getLastInsertId();
  }

  /**
   * Build the INSERT command.
   *
   * @param array $data The data to be inserted.
   * @return CommandBuilderInterface
   */
  private function buildInsertCommand(array $data)
  {
    $col = [];
    if ($this->is_assoc($data)) {
      $col = array_keys($data);
    } else {
      $col = array_keys($data[0]);
    }
    $command = "INSERT INTO " . $this->table . " (" . implode(",", $col) . ") VALUES ";
    if ($this->is_assoc($data)) {
      $command .= "('" . implode("','", array_values($data)) . "')";
    } else {
      foreach ($data as $row) {
        $command .= "('" . implode("','", array_values($row)) . "'),";
      }
      $command = rtrim($command, ',');
    }
    $this->commandString = $command;
    return $this;
  }

  /**
   * Delete rows from the table.
   *
   * @return int The number of affected rows.
   */
  public function delete(): int
  {
    $this->buildDeleteCommand();
    return DbConnection::getDb()->runQuery($this->commandString)->getCount();
  }

  /**
   * Build the DELETE command.
   *
   * @return CommandBuilderInterface
   */
  private function buildDeleteCommand()
  {
    $command = "DELETE FROM " . $this->table;
    if (!empty($this->where)) {
      $command .= $this->where;
    }
    $this->commandString = $command;
    return $this;
  }

  /**
   * Update rows in the table.
   *
   * @param array $data The data to be updated.
   * @return int The number of affected rows.
   */
  public function update(array $data): int
  {
    $this->buildUpdateCommand($data);
    return DbConnection::getDb()->runQuery($this->commandString)->getCount();
  }

  /**
   * Build the UPDATE command.
   *
   * @param array $data The data to be updated.
   * @return CommandBuilderInterface
   */
  private function buildUpdateCommand(array $data)
  {
    $command = "UPDATE " . $this->table . " SET ";
    foreach ($data as $key => $val) {
      $command .= $key . " = '" . $val . "',";
    }
    $command = rtrim($command, ',');
    if (!empty($this->where)) {
      $command .= $this->where;
    }
    $this->commandString = $command;
    return $this;
  }

  /**
   * Get the command string.
   *
   * @return string The command string.
   */
  public function getCommandString(): string
  {
    $this->buildSelectCommand();
    return $this->commandString;
  }

  /**
   * Reset all the clauses and variables.
   *
   * @return void
   */
  public function resetClause(): void
  {
    $this->table = '';
    $this->select = [];
    $this->where = '';
    $this->join = '';
    $this->orderBy = '';
    $this->groupBy = '';
    $this->having = '';
    $this->offset = '';
    $this->limit = '';
    $this->commandString = '';
    $this->append_and_keyword = false;
    $this->append_or_keyword = false;
  }

  /**
   * Check if an array is associative.
   *
   * @param array $data The array to check.
   * @return bool
   */
  private function is_assoc(array $data): bool
  {
    return array_keys($data) !== range(0, count($data) - 1);
  }
}
