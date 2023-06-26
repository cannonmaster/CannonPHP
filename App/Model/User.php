<?php

namespace App\Model;

/**
 * Class User
 *
 * Represents a User model.
 *
 * @package App\Model
 */
class User extends \App\DB\ORM\Model
{
    /**
     * The database table associated with the model.
     *
     * @var string
     */
    protected $table = 'User';

    /**
     * Get the profile associated with the user.
     *
     * @return \App\Model\Profile
     */
    public function profile()
    {
        return $this->hasOne('App\Model\Profile');
    }

    /**
     * Get the posts associated with the user.
     *
     * @return \App\Model\Post[]
     */
    public function posts()
    {
        return $this->hasMany('App\Model\Post', 'userId');
    }

    /**
     * Get the posts that the user has rated.
     *
     * @return \App\Model\Post[]
     */
    public function ratedPosts()
    {
        return $this->belongsToMany('App\Model\Post', 'ratings', 'userId', 'postId')->withPivot('ratings');
    }
}
