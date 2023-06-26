<?php

namespace App\Model;

/**
 * Class Profile
 *
 * Represents a Profile model.
 *
 * @package App\Model
 */
class Profile extends \App\DB\ORM\Model
{
  /**
   * The database table associated with the model.
   *
   * @var string
   */
  protected $table = 'Profile';

  /**
   * Get the user associated with the profile.
   *
   * @return \App\Model\User
   */
  public function user()
  {
    return $this->belongsTo('App\Model\User', 'id');
  }
}
