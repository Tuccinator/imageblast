<?php
namespace App\GraphQL\Mutation;

use GraphQL;
use GraphQL\Type\Definition\Type;
use Folklore\GraphQL\Support\Mutation;
use App\Group;
use App\GroupUser;
use Auth;

class GroupPrivacyMutation extends Mutation
{
    protected $attributes = [
        'name' => 'groupPrivacy'
    ];

    public function type()
    {
        return GraphQL::type('Group');
    }

    public function args()
    {
        return [
            'id' => [
                'type' => Type::nonNull(Type::int()),
                'description' => 'ID of group',
                'rules' => ['required']
            ],
            'privacy' => [
                'type' => Type::int(),
                'description' => 'Privacy of group',
                'rules' => ['required', 'in:0,1']
            ]
        ];
    }

    public function resolve($root, $args)
    {
        if(!Auth::check()) {
            return null;
        }

        $id = $args['id'];
        $public = $args['privacy'];

        // get group by ID
        $group = Group::find($id);

        // if group wasn't found, return null
        if(is_null($group)) {
            return null;
        }

        // check if the user is a member of the group
        $groupAttendance = GroupUser::where('user_id', Auth::id())->where('group_id', $group->id)->first();

        if(is_null($groupAttendance)) {
            return null;
        }

        // check if the user is an admin in the group
        if(!$groupAttendance->isAdmin()) {
            return null;
        }

        $group->public = $public;

        // make sure the group was saved
        if(!$group->save()) {
            return null;
        }

        return $group;
    }
}
