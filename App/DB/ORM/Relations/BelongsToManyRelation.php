<?php

namespace App\DB\ORM\Relations;

use App\DB\Db;
use App\DB\ORM\Model;
use Core\Model as DbConnection;

/**
 * Class BelongsToManyRelation
 * Represents a many-to-many relationship where the current model belongs to many related models.
 */
class BelongsToManyRelation extends Relation
{
  protected $type = 'belongs_to_many';
  protected $reference_table;
  protected $relation_table;
  protected $pivot_table;
  protected $reference_table_foreignKey;
  protected $relation_table_foreignKey;
  protected $reference_table_primaryKey;
  protected $relation_table_primaryKey;
  protected $pivot_columns = [];

  /**
   * BelongsToManyRelation constructor.
   *
   * @param string $referenceTable The name of the reference table.
   * @param string $pivotTable The name of the pivot table.
   * @param string $relationTable The name of the relation table.
   * @param string $referenceTableForeignKey The foreign key column name in the reference table.
   * @param string $relationTableForeignKey The foreign key column name in the relation table.
   * @param string $referenceTableLocalKey The local key column name in the reference table.
   * @param string $relationTableLocalKey The local key column name in the relation table.
   */
  public function __construct(
    $referenceTable,
    $pivotTable,
    $relationTable,
    $referenceTableForeignKey,
    $relationTableForeignKey,
    $referenceTableLocalKey,
    $relationTableLocalKey
  ) {
    parent::__construct();
    $this->reference_table = $referenceTable;
    $this->relation_table = $relationTable;
    $this->pivot_table = $pivotTable;
    $this->reference_table_foreignKey = $referenceTableForeignKey;
    $this->relation_table_foreignKey = $relationTableForeignKey;
    $this->reference_table_primaryKey = $referenceTableLocalKey;
    $this->relation_table_primaryKey = $relationTableLocalKey;
  }

  /**
   * Add pivot table data to the result.
   *
   * @param array $data The data to add pivot table data to.
   * @return array The data with added pivot table data.
   */
  public function addPivotData($data)
  {
    if (count($this->pivot_columns)) {
      foreach ($data as $key => $data_object) {
        $pivot = new \stdClass;
        foreach ($this->pivot_columns as $col) {
          $pivot->$col = $data_object->$col;
          unset($data_object->$col);
        }
        $data_object->pivot = clone $pivot;
        $data[$key] = $data_object;
      }
    }

    return $data;
  }

  /**
   * Get the first record from the result set with pivot table data.
   *
   * @return mixed The first record with pivot table data.
   */
  public function first()
  {
    $model = parent::first();
    $data = $this->addPivotData([$model]);

    return current($data);
  }

  /**
   * Get all the records from the result set with pivot table data.
   *
   * @return array All the records with pivot table data.
   */
  public function get(): array
  {
    $data = parent::get();
    return $this->addPivotData($data);
  }

  /**
   * Build the query to retrieve the relation data.
   *
   * @param array $data The data to build the query for.
   * @return $this The current BelongsToManyRelation instance.
   */
  public function buildRelationDataQuery($data)
  {
    $primary_key = $this->reference_table_primaryKey;
    $ids = array_map(function ($data) use ($primary_key) {
      return $data->{$primary_key};
    }, $data);
    $this->whereIn($this->pivot_table . '.' . $this->reference_table_foreignKey, $ids);

    return $this;
  }
  /**
   * Add relation data to the reference model.
   *
   * @param string $relationName The name of the relation.
   * @param array $data The reference model data.
   * @param array $relation_data The relation data.
   * @return array The reference model data with added relation data.
   */
  public function addRelationData($relationName, $data, $relation_data)
  {
    $reference_table_primaryKey = $this->reference_table_primaryKey;
    $reference_table_foreignKey = $this->reference_table_foreignKey;
    foreach ($data as $key => $reference_model) {
      $filtered_data = array_filter(
        $relation_data,
        function ($relation_obj) use ($reference_table_primaryKey, $reference_table_foreignKey, $reference_model) {
          return $reference_model->$reference_table_primaryKey == $relation_obj->pivot->$reference_table_foreignKey;
        }
      );
      $reference_model->$relationName = $filtered_data;
      $data[$key] = $reference_model;
    }
    return $data;
  }

  /**
   * Specify the columns to include from the pivot table.
   *
   * @param string|array $cols The pivot table columns to include.
   * @return $this The current BelongsToManyRelation instance.
   */
  public function withPivot($cols)
  {
    $cols = is_array($cols) ? $cols : [$cols];
    foreach ($cols as $col) {
      array_push($this->pivot_columns, $col);
    }

    if (count($this->pivot_columns)) {
      $this->select($this->model->getTable() . '.*');
      foreach ($this->pivot_columns as $col) {
        $this->select($this->pivot_table . '.' . $col);
      }
    }
    return $this;
  }

  /**
   * Attach records to the pivot table.
   *
   * @param array $data The data to attach to the pivot table.
   * @return int The number of affected rows.
   */
  public function attach($data)
  {
    $insertable_data = [];
    $referenceModel = $this->referenceModel;
    $reference_table_primaryKey = $this->reference_table_primaryKey;

    foreach ($data as $key => $value) {
      $insertable_row = [];
      $insertable_row[$this->reference_table_foreignKey] = $referenceModel->$reference_table_primaryKey;

      if (is_array($value)) {
        $insertable_row[$this->relation_table_foreignKey] = $key;
        foreach ($value as $vk => $vv) {
          $insertable_row[$vk] = $vv;
        }
      } else {
        $insertable_row[$this->relation_table_foreignKey] = $value;
      }
      array_push($insertable_data, $insertable_row);
    }
    $affected_row = 0;
    foreach ($insertable_data as $row) {
      $res = $this->cb->table($this->pivot_table)->insert($row);
      $affected_row++;
    }
    return $affected_row;
  }

  /**
   * Detach records from the pivot table.
   *
   * @param array $data The data to detach from the pivot table.
   * @return int The number of deleted rows.
   */
  public function detach(array $data)
  {
    $reference_model = $this->referenceModel;
    $reference_table_primaryKey = $this->reference_table_primaryKey;

    $cb = $this->cb->table($this->pivot_table);
    $cb->where($this->reference_table_foreignKey, '=', $reference_model->$reference_table_primaryKey);
    $cb->whereIn($this->relation_table_foreignKey, $data);
    return $cb->delete();
  }

  /**
   * Initiate the connection for the relation.
   *
   * @return $this The current BelongsToManyRelation instance.
   */
  public function initiateConnection()
  {
    if (!$this->connectionInitiated) {
      $this->join($this->pivot_table, $this->pivot_table . '.' . $this->relation_table_foreignKey, '=', $this->relation_table . '.' . $this->relation_table_primaryKey);

      $this->join($this->reference_table, $this->pivot_table . '.' . $this->reference_table_foreignKey, '=', $this->reference_table . '.' . $this->reference_table_primaryKey);
      $this->connectionInitiated = true;
    }
    $referenceModel = $this->referenceModel;
    if (!empty($this->referenceModel)) {
      $reference_table_primaryKey = $this->reference_table_primaryKey;
      $this->where($this->pivot_table . '.' . $this->reference_table_foreignKey, '=', $referenceModel->$reference_table_primaryKey);
    }
    $this->withPivot($this->reference_table_foreignKey);
    return $this;
  }
}
