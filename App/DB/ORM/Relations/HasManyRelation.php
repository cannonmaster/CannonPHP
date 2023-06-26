<?php

namespace App\DB\ORM\Relations;

/**
 * Class HasManyRelation
 * Represents a "has many" relationship between two database tables.
 *
 * @package App\DB\ORM\Relations
 */
class HasManyRelation extends Relation
{
  /**
   * @var string The type of relationship.
   */
  protected $type = 'has_many';

  /**
   * @var string The reference table name.
   */
  protected $referenceTable;

  /**
   * @var string The relation table name.
   */
  protected $relationTable;

  /**
   * @var string The foreign key column name.
   */
  protected $foreignKey;

  /**
   * @var string The local key column name.
   */
  protected $localKey;

  /**
   * HasManyRelation constructor.
   *
   * @param string $referenceTable The reference table name.
   * @param string $relationTable The relation table name.
   * @param string $foreignKey The foreign key column name.
   * @param string $localKey The local key column name.
   */
  public function __construct($referenceTable, $relationTable, $foreignKey, $localKey)
  {
    parent::__construct();
    $this->referenceTable = $referenceTable;
    $this->localKey = $localKey;
    $this->foreignKey = $foreignKey;
    $this->relationTable = $relationTable;
  }

  /**
   * Initializes the database connection and sets up the query conditions based on the reference model.
   *
   * @return $this
   */
  public function initiateConnection()
  {
    $referenceModel = $this->referenceModel;
    if (!$this->connectionInitiated && !empty($referenceModel)) {
      $localKey = $this->localKey;
      $this->where($this->relationTable . '.' . $this->foreignKey, '=', $referenceModel->$localKey);
      $this->connectionInitiated = true;
    }

    return $this;
  }

  /**
   * Builds the query to fetch the relation data based on the given data.
   *
   * @param array $data The reference model data.
   * @return $this
   */
  public function buildRelationDataQuery($data)
  {
    $ids = array_map(function ($item) {
      return $item[$this->localKey];
    }, $data);
    $this->whereIn($this->relationTable . '.' . $this->foreignKey, $ids);
    return $this;
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
    $localKey = $this->localKey;
    $foreignKey = $this->foreignKey;
    foreach ($data as $key => $referenceModel) {
      $filtered_relation_data = array_filter($relation_data, function ($relation_data_object) use ($foreignKey, $localKey, $referenceModel) {
        return $referenceModel->$localKey == $relation_data_object->$foreignKey;
      });
      $referenceModel->$relationName = $filtered_relation_data;
      $data[$key] = $referenceModel;
    }
    return $data;
  }

  /**
   * Creates a new record in the relation table associated with the reference model.
   *
   * @param array $data The data to be inserted into the relation table.
   * @return mixed The result of the create operation.
   */
  public function create($data)
  {
    $foreign_key = $this->foreignKey;
    $local_key = $this->localKey;
    $referenceModel = $this->referenceModel;
    $data[$foreign_key] = $referenceModel->$local_key;
    return parent::create($data);
  }
}
