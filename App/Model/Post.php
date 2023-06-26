<?php

namespace App\Model;

/**
 * Class Post
 *
 * Represents a Post model.
 *
 * @package App\Model
 */
class Post extends \App\DB\ORM\Model
{
  /**
   * The database table associated with the model.
   *
   * @var string
   */
  protected $table = 'Posts';

  /**
   * Get the user associated with the post.
   *
   * @return \App\Model\User
   */
  public function user()
  {
    return $this->belongsTo('App\Model\User', 'userId');
  }

  // Uncomment the code below to enable the `getAll` method.

  // /**
  //  * Get all the posts.
  //  *
  //  * @return array
  //  */
  // public static function getAll()
  // {
  //     try {
  //         $db = static::getDb();
  //         // Add your logic here to fetch all the posts
  //         // and return them as an array.
  //     } catch (PDOException $e) {
  //         // Handle the exception if necessary.
  //     }
  // }
}
