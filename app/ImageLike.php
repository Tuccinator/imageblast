<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ImageLike extends Model
{
    protected $table = 'image_likes';

    public function image()
    {
        return $this->belongsTo('App\Image');
    }
}
