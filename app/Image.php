<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Image extends Model
{
    protected $table = 'images';

    const LIKE = 1;
    const DISLIKE = 2;

    public function reverseLike($type)
    {
        if($type === self::LIKE) {
            $this->likes = $this->likes - 1;

            return;
        }

        $this->dislikes = $this->dislikes - 1;
    }

    public function addLike($type)
    {
        if($type === self::LIKE) {
            $this->likes = $this->likes + 1;

            return;
        }

        $this->dislikes = $this->dislikes + 1;
    }

    public function isPublic()
    {
        return $this->private === 0;
    }

    public function isFriendsOnly()
    {
        return $this->private === 1;
    }

    public function isPrivate()
    {
        return $this->private === 2;
    }
}
