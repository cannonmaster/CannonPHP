<?php
class Post extends \Core\Model
{
    public static function getAll()
    {
        try {
            $db = static::getDb();
        } catch (PDOException $e) {
        }
    }
}
