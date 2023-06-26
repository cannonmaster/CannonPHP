<?php

namespace App\DB;

use App\DB\QueryResult;

interface CommandBuilderInterface
{
  /**
   * Set the table to perform the query on.
   *
   * @param string $table The name of the table.
   * @return self
   */
  public function table(string $table): self;

  /**
   * Set the columns to select.
   *
   * @param string|array $select The columns to select.
   * @return self
   */
  public function select(string|array $select): self;

  /**
   * Add an "ORDER BY" clause to the query.
   *
   * @param string $col       The column to order by.
   * @param string $direction The sort direction ("ASC" or "DESC").
   * @return self
   */
  public function orderBy(string $col, string $direction): self;

  /**
   * Set the maximum number of rows to retrieve.
   *
   * @param int $limit The maximum number of rows to retrieve.
   * @return self
   */
  public function limit(int $limit): self;

  /**
   * Set the number of rows to skip.
   *
   * @param int $offset The number of rows to skip.
   * @return self
   */
  public function offset(int $offset): self;

  /**
   * Add a "WHERE" clause to the query.
   *
   * @param string $col      The column to compare.
   * @param string $operator The comparison operator.
   * @param mixed  $value    The value to compare.
   * @return self
   */
  public function where(string $col, string $operator, mixed $value): self;

  /**
   * Add an "OR WHERE" clause to the query.
   *
   * @param string $col      The column to compare.
   * @param string $operator The comparison operator.
   * @param mixed  $value    The value to compare.
   * @return self
   */
  public function orWhere(string $col, string $operator, mixed $value): self;

  /**
   * Add a "WHERE IN" clause to the query.
   *
   * @param string $col    The column to compare.
   * @param array  $values The values to compare against.
   * @return self
   */
  public function whereIn(string $col, array $values): self;

  /**
   * Add an "OR WHERE IN" clause to the query.
   *
   * @param string $col    The column to compare.
   * @param array  $values The values to compare against.
   * @return self
   */
  public function orWhereIn(string $col, array $values): self;

  /**
   * Add a "JOIN" clause to the query.
   *
   * @param string $table    The table to join.
   * @param string $col1     The column on the first table.
   * @param string $operator The comparison operator.
   * @param string $col2     The column on the second table.
   * @return self
   */
  public function join(string $table, string $col1, string $operator, string $col2): self;

  /**
   * Execute the query and retrieve the results.
   *
   * @param string $class The class to instantiate for each result row.
   * @return QueryResult
   */
  public function get(string $class): QueryResult;
  /**
   * Calculate the average value of a column.
   *
   * @param string $col The column to calculate the average on.
   * @return mixed The average value.
   */
  public function avg(string $col): mixed;

  /**
   * Calculate the sum of a column.
   *
   * @param string $col The column to calculate the sum on.
   * @return mixed The sum value.
   */
  public function sum(string $col): mixed;

  /**
   * Count the number of rows.
   *
   * @param string $col The column to count.
   * @return mixed The count value.
   */
  public function count(string $col): mixed;

  /**
   * Find the minimum value of a column.
   *
   * @param string $col The column to find the minimum on.
   * @return mixed The minimum value.
   */
  public function min(string $col): mixed;

  /**
   * Find the maximum value of a column.
   *
   * @param string $col The column to find the maximum on.
   * @return mixed The maximum value.
   */
  public function max(string $col): mixed;

  /**
   * Insert a new row into the table.
   *
   * @param array $data The data to insert.
   * @return int The number of affected rows.
   */
  public function insert(array $data): int;

  /**
   * Insert a new row into the table and retrieve the auto-increment ID.
   *
   * @param array $data The data to insert.
   * @return string The auto-increment ID.
   */
  public function insertGetId(array $data): string;

  /**
   * Delete rows from the table based on the current query.
   *
   * @return int The number of affected rows.
   */
  public function delete(): int;

  /**
   * Update rows in the table based on the current query.
   *
   * @param array $data The data to update.
   * @return int The number of affected rows.
   */
  public function update(array $data): int;

  /**
   * Get the generated SQL command string.
   *
   * @return string The SQL command string.
   */
  public function getCommandString(): string;
}
