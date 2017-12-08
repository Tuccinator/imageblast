<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    protected $table = 'groups';

    public function members()
    {
        return $this->hasMany('App\GroupUser')->join('users', 'users.id', '=', 'group_users.user_id');
    }
}
