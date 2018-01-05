<?php
namespace App\GraphQL\Mutation;

use GraphQL;
use GraphQL\Type\Definition\Type;
use Folklore\GraphQL\Support\Mutation;
use App\User;
use App\GroupUser;
use App\Group;
use Auth;

class JoinGroupMutation extends Mutation
{
    protected $attributes = [
        'name' => 'joinGroup'
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
                'description' => 'Invite code for the group'
            ]
        ];
    }

    public function resolve($root, $args)
    {
        $groupId = $args['id'];

        // check if user is logged in to join/leave group
        if(!Auth::check()) {
            return null;
        }

        // get the group by ID
        $group = Group::find($groupId);

        // check if the group actually exists
        if(is_null($group)) {
            return null;
        }

        // check if the user is already in the group
        $groupAttendance = GroupUser::where('group_id', $group->id)->where('user_id', Auth::id())->first();

        // if the user is in the group, remove them from it
        if(!is_null($groupAttendance)) {

            // user cannot leave group if they are creator
            if($group->creator_id === Auth::id()) {
                return null;
            }

            $groupAttendance->delete();

            return Group::where('id', $group->id)->with('members')->first();
        }

        // check if the group is private
        //
        // if it is private, make sure the user has provided the invite code
        if(!$group->isPublic()) {
            if(empty($args['code'])) {
                return null;
            }

            if($args['code'] !== $group->invite_code) {
                return null;
            }
        }

        // join the group
        $groupUser = new GroupUser;
        $groupUser->group_id = $group->id;
        $groupUser->user_id = Auth::id();
        $groupUser->invite_code = null;
        $groupUser->status = 0;
        $groupUser->status_end = null;
        $groupUser->role = 0;

        if(!$groupUser->save()) {
            return null;
        }

        return Group::where('id', $group->id)->with('members')->first();
    }
}
