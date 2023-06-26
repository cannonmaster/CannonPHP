<?php

namespace App\DB\ORM\Relations;

/**
 * Class BelongsToRelation
 * Represents a "belongs to" relationship between two database tables.
 *
 * @package App\DB\ORM\Relations
 */
class BelongsToRelation extends Relation
{
  /**
   * @var string The reference table name.
   */
  public $reference_table;

  /**
   * @var string The relation table name.
   */
  public $relation_table;

  /**
   * @var string The foreign key column name.
   */
  public $foreign_key;

  /**
   * @var string The local key column name.
   */
  public $local_key;

  /**
   * BelongsToRelation constructor.
   *
   * @param string $reference_table The reference table name.
   * @param string $relation_table The relation table name.
   * @param string $foreign_key The foreign key column name.
   * @param string $local_key The local key column name.
   */
  public function __construct($reference_table, $relation_table, $foreign_key, $local_key)
  {
    parent::__construct();
    $this->reference_table = $reference_table;
    $this->relation_table = $relation_table;
    $this->foreign_key = $foreign_key;
    $this->local_key = $local_key;
  }

  /**
   * Initializes the database connection and sets up the query conditions based on the reference model.
   *
   * @return $this
   */
  public function initiateConnection()
  {
    $reference_model = $this->referenceModel;
    if (!$this->connectionInitiated && !empty($reference_model)) {
      $foreign_key = $this->foreign_key;
      $this->where($this->relation_table . '.' . $this->local_key, '=', $reference_model->$foreign_key);
      $this->connectionInitiated = true;
    }
    return $this;
  }

  /**
   * Builds the query to fetch the relation data based on the given data.
   *
   * @param array $data The reference model data.
   * @return void
   */
  public function buildRelationDataQuery($data)
  {
    $ids = array_map(function ($item) {
      return $item->{$this->local_key};
    }, $data);
    $this->whereIn($this->relation_table . '.' . $this->foreign_key, $ids);
  }

  /**
   * Adds the relation data to the reference models.
   *
   * @param string $relationName The name of the relation.
   * @param array $data The reference model data.
   * @param array $relation_data The relation data.
   * @return array The updated reference model data.
   */
  public function addRelationData($relationName, $data, $relation_data)
  {
    $local_key = $this->local_key;
    $foreign_key = $this->foreign_key;
    foreach ($data as $key => $reference_model) {
      $filtered_relation_data = array_filter($relation_data, function ($relation_data_obj) use ($local_key, $foreign_key, $reference_model) {
        return  $reference_model->$foreign_key === $relation_data_obj->$local_key;
      });
      if (count($filtered_relation_data)) {
        $reference_model->$relationName = current($filtered_relation_data);
      } else {
        $reference_model->$relationName = null;
      }
      $data[$key] = $reference_model;
    }
    return $data;
  }
}
