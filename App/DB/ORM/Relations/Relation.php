<?php

namespace App\DB\ORM\Relations;

use App\DB\ORM\Builder;

abstract class Relation extends Builder
{
  protected $type = '';
  protected $connectionInitiated = false;
  protected $referenceModel = null;

  public function __construct()
  {
    parent::__construct();
  }

  /**
   * Get the reference model instance.
   *
   * @return mixed
   */
  public function getReferenceModel()
  {
    return $this->referenceModel;
  }

  /**
   * Set the reference model instance.
   *
   * @param mixed $referenceModel
   * @return $this
   */
  public function referenceModel($referenceModel)
  {
    $this->referenceModel = $referenceModel;
    return $this;
  }

  /**
   * Initiate the connection for the relation.
   *
   * @return $this
   */
  public abstract function initiateConnection();

  /**
   * Build the query to fetch the relation data.
   *
   * @param array $data
   * @return $this
   */
  public abstract function buildRelationDataQuery($data);

  /**
   * Add the relation data to the reference models.
   *
   * @param string $relationName
   * @param array $data
   * @param array $relation_data
   * @return array
   */
  public abstract function addRelationData($relationName, $data, $relation_data);
}
