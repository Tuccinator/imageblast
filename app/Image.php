<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Image extends Model
{
    protected $table = 'images';

    const LIKE = 1;
    const DISLIKE = 2;

    public function user()
    {
        return $this->belongsTo('App\User');
    }

    /**
     * Reverse a previous like/dislike
     */
    public function reverseLike($type)
    {
        if($type === self::LIKE) {
            $this->likes = $this->likes - 1;

            return;
        }

        $this->dislikes = $this->dislikes - 1;
    }

    /**
     * Add a new like/dislike
     */
    public function addLike($type)
    {
        if($type === self::LIKE) {
            $this->likes = $this->likes + 1;

            return;
        }

        $this->dislikes = $this->dislikes + 1;
    }

    /**
     * Check if the image belongs to a group
     */
    public function isGroup()
    {
        return $this->group_id !== null;
    }

    /**
     * Check if the image is public
     */
    public function isPublic()
    {
        return $this->private === 0;
    }

    /**
     * Check if the image is followers only
     */
    public function isFollowersOnly()
    {
        return $this->private === 1;
    }

    /**
     * Check if image is completely private
     */
    public function isPrivate()
    {
        return $this->private === 2;
    }
}
