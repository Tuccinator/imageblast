<?php
namespace App\GraphQL\Mutation;

use GraphQL;
use GraphQL\Type\Definition\Type;
use Folklore\GraphQL\Support\Mutation;
use App\User;
use App\GroupUser;
use App\Group;
use Auth;

class ChangeGroupCodeMutation extends Mutation
{
    protected $attributes = [
        'name' => 'changeGroupCode'
    ];

    public function type()
    {
        return GraphQL::type('Group');
    }

    public function args()
    {
        return [
            'id' => [
                'type' => Type::int(),
                'description' => 'ID of group',
                'rules' => ['required', 'exists:groups,id']
            ],
            'code' => [
                'type' => Type::string(),
                'description' => 'Invite code of group',
                'rules' => ['required', 'max:32']
            ]
        ];
    }

    public function resolve($root, $args)
    {
        $groupId = $args['id'];
        $code = $args['code'];

        // check if user is logged in to join/leave group
        if(!Auth::check()) {
            return null;
        }

        // get the group by ID
        $group = Group::find($groupId);

        // check if the group actually exists
        if(is_null($group)) {
            echo 'uhoh2';
            return null;
        }

        // make sure the group is private
        if($group->isPublic()) {
            echo 'uhoh3';
            return null;
        }

        // check if the user is an admin of the group
        $groupAttendance = GroupUser::where('group_id', $group->id)->where('user_id', Auth::id())->where('role', 2)->first();

        // if the user is in the group, remove them from it
        if(is_null($groupAttendance)) {
            echo 'uhoh4';
            return null;
        }

        // check for unique code
        $groupCount = Group::where('invite_code', $code)->count();
        $groupUserCount = GroupUser::where('group_id', $group->id)->where('invite_code', $code)->count();

        if($groupCount + $groupUserCount > 0) {
            return null;
        }

        $group->invite_code = $code;

        if(!$group->save()) {
            echo 'uhoh5';
            return null;
        }

        return Group::where('id', $group->id)->with('members')->first();
    }
}
