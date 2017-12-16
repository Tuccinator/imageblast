<?php
namespace App\GraphQL\Mutation;

use GraphQL;
use GraphQL\Type\Definition\Type;
use Folklore\GraphQL\Support\Mutation;
use App\Group;
use App\GroupUser;
use Auth;

class CreateGroupMutation extends Mutation
{
    protected $attributes = [
        'name' => 'createGroup'
    ];

    public function type()
    {
        return GraphQL::type('Group');
    }

    public function args()
    {
        return [
            'name' => [
                'name' => 'name',
                'type' => Type::string(),
                'rules' => ['required']
            ],
            'description' => [
                'name' => 'description',
                'type' => Type::string()
            ],
            'privacy' => [
                'name' => 'privacy',
                'type' => Type::int(),
                'rules' => ['required', 'in:0,1']
            ]
        ];
    }

    public function resolve($root, $args)
    {
        if(!Auth::check()) {
            return null;
        }

        $name = $args['name'];
        $description = $args['description'] ?: null;
        $public = $args['privacy'];

        $group = new Group;
        $group->creator_id = Auth::id();
        $group->name = $name;
        $group->description = $description;
        $group->public = $public;
        $group->invite_code = null;

        if(!$group->save()) {
            return null;
        }

        $groupUser = new GroupUser;
        $groupUser->group_id = $group->id;
        $groupUser->user_id = Auth::id();
        $groupUser->invite_code = null;
        $groupUser->status = 0;
        $groupUser->status_end = null;
        $groupUser->role = 2;
        $groupUser->save();

        return $group;
    }
}
