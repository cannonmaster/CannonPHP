<?php

namespace App\DB\ORM;

use App\DB\CommandBuilderInterface;
use Core\Model as DbConnection;

class Builder
{
  protected $model;
  public $cb;
  protected $relations = [];

  /**
   * Builder constructor.
   *
   * @param CommandBuilderInterface|null $command_builder The command builder instance.
   */
  public function __construct(CommandBuilderInterface $command_builder = null)
  {
    if ($command_builder === null) {
      $command_builder = DbConnection::getCommandBuilder();
    }
    $this->cb = $command_builder;
  }

  /**
   * Get the command builder instance.
   *
   * @return CommandBuilderInterface The command builder instance.
   */
  public function getCb(): CommandBuilderInterface
  {
    return $this->cb;
  }

  /**
   * Set the model for the query.
   *
   * @param mixed $model The model instance.
   * @return $this The Builder instance.
   */
  public function model($model): self
  {
    $this->model = $model;
    return $this;
  }

  /**
   * Execute the query and get the results.
   *
   * @return array The query results.
   */
  public function get(): array
  {
    $data = $this->cb->table($this->model->getTable())->get(get_class($this->model))->getRows();
    if (count($data) && count($this->relations)) {
      foreach ($this->relations as $relationName => $relation) {
        $relation->buildRelationDataQuery($data);
        $relation_data = $relation->get();
        $data = $relation->addRelationData($relationName, $data, $relation_data);
      }
    }

    return $data;
  }

  /**
   * Add a WHERE clause to the query.
   *
   * @return $this The Builder instance.
   */
  public function where()
  {
    call_user_func_array([$this->cb, 'where'], func_get_args());
    return $this;
  }

  /**
   * Add an OR WHERE clause to the query.
   *
   * @return $this The Builder instance.
   */
  public function orWhere()
  {
    call_user_func_array([$this->cb, 'orWhere'], func_get_args());
    return $this;
  }

  /**
   * Add a WHERE IN clause to the query.
   *
   * @return $this The Builder instance.
   */
  public function whereIn()
  {
    call_user_func_array([$this->cb, 'whereIn'], func_get_args());
    return $this;
  }

  /**
   * Add an OR WHERE IN clause to the query.
   *
   * @return $this The Builder instance.
   */
  public function orWhereIn()
  {
    call_user_func_array([$this->cb, 'orWhereIn'], func_get_args());
    return $this;
  }

  /**
   * Specify the columns to be selected in the query.
   *
   * @return $this The Builder instance.
   */
  public function select()
  {
    call_user_func_array([$this->cb, 'select'], func_get_args());
    return $this;
  }

  /**
   * Set the offset for the query.
   *
   * @return $this The Builder instance.
   */
  public function offset()
  {
    call_user_func_array([$this->cb, 'offset'], func_get_args());
    return $this;
  }

  /**
   * Set the limit for the query.
   *
   * @return $this The Builder instance.
   */
  public function limit()
  {
    call_user_func_array([$this->cb, 'limit'], func_get_args());
    return $this;
  }

  /**
   * Set the order of the query results.
   *
   * @return $this The Builder instance.
   */
  public function orderBy()
  {
    call_user_func_array([$this->cb, 'orderBy'], func_get_args());
    return $this;
  }

  /**
   * Perform a join operation on the query.
   *
   * @return $this The Builder instance.
   */
  public function join()
  {
    call_user_func_array([$this->cb, 'join'], func_get_args());
    return $this;
  }

  /**
   * Get the SQL string for the query.
   *
   * @return string The SQL string.
   */
  public function toSql(): string
  {
    return $this->cb->table($this->model->getTable())->getCommandString();
  }

  /**
   * Calculate the average of a column.
   *
   * @return mixed The average value.
   */
  public function avg()
  {
    $this->cb->table($this->model->getTable());
    return call_user_func_array([$this->cb, 'avg'], func_get_args());
  }

  /**
   * Calculate the sum of a column.
   *
   * @return mixed The sum value.
   */
  public function sum()
  {
    $this->cb->table($this->model->getTable());
    return call_user_func_array([$this->cb, 'sum'], func_get_args());
  }

  /**
   * Count the number of records.
   *
   * @return mixed The count value.
   */
  public function count()
  {
    $this->cb->table($this->model->getTable());
    return call_user_func_array([$this->cb, 'count'], func_get_args());
  }

  /**
   * Get the maximum value of a column.
   *
   * @return mixed The maximum value.
   */
  public function max()
  {
    $this->cb->table($this->model->getTable());
    return call_user_func_array([$this->cb, 'max'], func_get_args());
  }

  /**
   * Get the minimum value of a column.
   *
   * @return mixed The minimum value.
   */
  public function min()
  {
    $this->cb->table($this->model->getTable());
    return call_user_func_array([$this->cb, 'min'], func_get_args());
  }

  /**
   * Get the first record from the query results.
   *
   * @return mixed|null The first record or null if not found.
   */
  public function first()
  {
    $this->limit(1);

    $data = $this->get();
    if (count($data) > 0)
      return current($data);

    return null;
  }

  /**
   * Find a record by its primary key value.
   *
   * @param mixed $id The primary key value.
   * @return mixed|null The found record or null if not found.
   */
  public function find($id)
  {
    return $this->where($this->model->getPrimaryKey(), '=', $id)->first();
  }

  /**
   * Create a new record in the database.
   *
   * @param array $data The data to be inserted.
   * @return mixed The created record.
   */
  public function create(array $data)
  {
    $new_id = $this->cb->table($this->model->getTable())->insertGetId($data);
    return call_user_func_array([$this, 'find'], [$new_id]);
  }

  /**
   * Delete records from the database.
   *
   * @return mixed The number of affected rows.
   */
  public function delete()
  {
    return $this->cb->table($this->model->getTable())->delete();
  }

  /**
   * Update records in the database.
   *
   * @param array $data The data to be updated.
   * @return mixed The number of affected rows.
   */
  public function update(array $data)
  {
    return $this->cb->table($this->model->getTable())->update($data);
  }

  /**
   * Eager load relationships for the query.
   *
   * @param array $relation The relationships to be loaded.
   * @return $this The Builder instance.
   */
  public function with(array $relation = [])
  {
    foreach ($relation as $relationKeyOrName => $relationNameOrClosure) {
      if (is_string($relationNameOrClosure)) {
        $relation = call_user_func_array([$this->model, $relationNameOrClosure], []);
        $this->relations[$relationNameOrClosure] = $relation;
      } else {
        $relation = call_user_func_array([$this->model, $relationKeyOrName], []);
        $relationNameOrClosure($relation);
        $this->relations[$relationKeyOrName] = $relation;
      }
    }
    return $this;
  }
}
