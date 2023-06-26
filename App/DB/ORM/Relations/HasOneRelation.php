<?php

namespace App\DB\ORM\Relations;

class HasOneRelation extends Relation
{
  protected $type = 'has_one';
  protected $reference_table;
  protected $relation_table;
  protected $foreign_key;
  protected $local_key;

  /**
   * Create a new HasOneRelation instance.
   *
   * @param string $reference_table The name of the reference table.
   * @param string $relation_table The name of the relation table.
   * @param string $foreign_key The foreign key column name in the relation table.
   * @param string $local_key The local key column name in the reference table.
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
   * Initialize the connection and set up the query conditions.
   *
   * @return $this
   */
  public function initiateConnection()
  {
    $reference_model = $this->referenceModel;
    if (!$this->connectionInitiated && !empty($reference_model)) {
      $local_key = $this->local_key;
      $this->where($this->relation_table . '.' . $this->foreign_key, '=', $reference_model->$local_key);
      $this->connectionInitiated = true;
    }

    return $this;
  }

  /**
   * Build the query to retrieve the relation data.
   *
   * @param array $data The data of the reference model.
   * @return void
   */
  public function buildRelationDataQuery($data)
  {
    $ids = array_map(function ($item) {
      return $item[$this->local_key];
    }, $data);
    $this->whereIn($this->relation_table . '.' . $this->foreign_key, $ids);
  }

  /**
   * Add the relation data to the reference models.
   *
   * @param string $relationName The name of the relation.
   * @param array $data The data of the reference models.
   * @param array $relation_data The relation data.
   * @return array The updated reference models.
   */
  public function addRelationData($relationName, $data, $relation_data)
  {
    $foreign_key = $this->foreign_key;
    $local_key = $this->local_key;
    foreach ($data as $key => $reference_model) {
      $filtered_relation_data = array_filter($relation_data, function ($relation_data_obj) use ($reference_model, $foreign_key, $local_key) {
        return $reference_model->$local_key === $relation_data_obj->$foreign_key;
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

  /**
   * Create a new record in the relation table associated with the reference model.
   *
   * @param array $data The data to be inserted into the relation table.
   * @return mixed The result of the create operation.
   */
  public function create($data)
  {
    $foreign_key = $this->foreign_key;
    $local_key = $this->local_key;
    $referenceModel = $this->referenceModel;
    $data[$foreign_key] = $referenceModel->$local_key;
    return parent::create($data);
  }
}
