<?php

namespace App\DB\ORM;

use App\DB\ORM\Relations\BelongsToManyRelation;
use App\DB\ORM\Relations\BelongsToRelation;
use App\DB\ORM\Relations\HasManyRelation;
use App\DB\ORM\Relations\HasOneRelation;
use Core\Model as DbConnection;

class Model
{
  private $data = [];
  protected $table = "";
  protected $primary_key = "id";
  protected static $builder_callable_methods = ['all'];

  /**
   * Model constructor.
   */
  public function __construct()
  {
  }

  /**
   * Handle dynamic method calls on the model.
   *
   * @param string $method The method name.
   * @param array $arguments The method arguments.
   * @return mixed The result of the method call.
   */
  public static function __callStatic($method, $arguments)
  {
    $model = get_called_class();
    $builder = new Builder();
    if (method_exists($builder, $method)) {
      $model_instance = new $model;
      $builder->model($model_instance);
      return call_user_func_array([$builder, $method], $arguments);
    }
    return call_user_func_array([$model, $method], $arguments);
  }

  /**
   * Get the primary key of the model.
   *
   * @return string The primary key column name.
   */
  public function getPrimaryKey()
  {
    return $this->primary_key;
  }

  /**
   * Get the table name of the model.
   *
   * @return string The table name.
   */
  public function getTable()
  {
    return $this->table;
  }

  /**
   * Remove the current record from the database.
   *
   * @return bool True if the record is successfully removed, false otherwise.
   */
  public function remove()
  {
    $pk = $this->primary_key;
    if (!isset($this->primary_key)) {
      return false;
    }
    return DbConnection::getDb()->table($this->table)->where($this->primary_key, '=', $this->$pk)->delete();
  }

  /**
   * Update the current record in the database.
   *
   * @param array $data The data to be updated.
   * @return bool True if the record is successfully updated, false otherwise.
   */
  public function updateSingle(array $data)
  {
    $pk = $this->primary_key;
    if (!isset($this->$pk)) {
      return false;
    }
    return DbConnection::getDb()->table($this->table)->where($this->primary_key, '=', $this->$pk)->update($data);
  }

  /**
   * Save the model to the database.
   *
   * @return mixed The created or updated record.
   */
  public function save()
  {
    $data = DbConnection::getDb()->runQuery('SHOW COLUMNS FROM ' . $this->table)->getRows();
    $cols = array_column($data, 'Field');

    foreach ($this->data as $key => $val) {
      $filtered = array_filter($cols, function ($col) use ($key) {
        return $col === $key;
      });
      if (!count($filtered)) {
        unset($this->data[$key]);
      }
    }

    $pk = $this->primary_key;
    if (!is_null($this->$pk)) {
      return DbConnection::getDb()->table($this->table)->where($this->primary_key, '=', $this->$pk)->update($this->data);
    } else {
      $id = DbConnection::getDb()->table($this->table)->insertGetId($this->data);
      $this->primary_key = $id;
      return $id;
    }
  }

  /**
   * Define a has-many relationship.
   *
   * @param string $relationClass The class name of the related model.
   * @param string $foreignKey The foreign key column name in the related table.
   * @param string|null $localKey The local key column name in the current table (optional).
   * @return \App\DB\ORM\Relations\HasManyRelation The has-many relationship object.
   */
  public function hasMany($relationClass, $foreignKey, $localKey = null)
  {
    $localKey = !empty($localKey) ? $localKey : $this->primary_key;
    $relation_model = new $relationClass;
    $relation = new HasManyRelation($this->table, $relation_model->getTable(), $foreignKey, $localKey);
    $relation->model($relation_model);
    $primary_key = $this->primary_key;

    if (isset($this->data[$primary_key])) {
      $relation->referenceModel($this);
    }

    $relation->initiateConnection();
    return $relation;
  }

  /**
   * Define a belongs-to relationship.
   *
   * @param string $relationClass The class name of the related model.
   * @param string $foreignKey The foreign key column name in the current table.
   * @param string|null $localKey The local key column name in the related table (optional).
   * @return \App\DB\ORM\Relations\BelongsToRelation The belongs-to relationship object.
   */
  public function belongsTo($relationClass, $foreignKey, $localKey = null)
  {
    $local_key = !empty($localKey) ? $localKey : $this->primary_key;
    $relation_model = new $relationClass();
    $relation = new BelongsToRelation($this->table, $relation_model->getTable(), $foreignKey, $local_key);
    $relation->model($relation_model);
    $primary_key = $this->primary_key;

    if (isset($this->data[$primary_key])) {
      $relation->referenceModel($this);
    }

    $relation->initiateConnection();
    return $relation;
  }

  /**
   * Define a has-one relationship.
   *
   * @param string $relationClass The class name of the related model.
   * @return \App\DB\ORM\Relations\HasOneRelation The has-one relationship object.
   */
  public function hasOne($relationClass)
  {
    $primary_key = $this->primary_key;
    $relation_model = new $relationClass;
    $relation = new HasOneRelation($this->table, $relation_model->getTable(), $relation_model->getPrimaryKey(), $primary_key);
    $relation->model($relation_model);

    if (isset($this->data[$primary_key])) {
      $relation->referenceModel($this);
    }

    $relation->initiateConnection();
    return $relation;
  }

  /**
   * Define a belongs-to-many relationship.
   *
   * @param string $relationClass The class name of the related model.
   * @param string $pivotTable The pivot table name.
   * @param string $referenceTableForeignKey The foreign key column name in the reference table.
   * @param string $relationTableForeignKey The foreign key column name in the relation table.
   * @param string|null $localKey The local key column name in the current table (optional).
   * @return \App\DB\ORM\Relations\BelongsToManyRelation The belongs-to-many relationship object.
   */
  public function belongsToMany(
    $relationClass,
    $pivotTable,
    $referenceTableForeignKey,
    $relationTableForeignKey,
    $localKey = null
  ) {
    $primary_key = $this->primary_key;
    $local_key = !empty($localKey) ? $localKey : $primary_key;
    $relation_model = new $relationClass;
    $relation = new BelongsToManyRelation(
      $this->table,
      $pivotTable,
      $relation_model->getTable(),
      $referenceTableForeignKey,
      $relationTableForeignKey,
      $local_key,
      $relation_model->getPrimaryKey()
    );
    $relation->model($relation_model);

    if (isset($this->data[$primary_key])) {
      $relation->referenceModel($this);
    }

    $relation->initiateConnection();
    return $relation;
  }

  /**
   * Magic method to set values to the model's properties.
   *
   * @param string $name The property name.
   * @param mixed $value The property value.
   */
  public function __set($name, $value)
  {
    $this->data[$name] = $value;
  }

  /**
   * Magic method to get values from the model's properties.
   *
   * @param string $name The property name.
   * @return mixed|null The property value if it exists, null otherwise.
   */
  public function __get($name)
  {
    return $this->data[$name] ?? null;
  }
}
