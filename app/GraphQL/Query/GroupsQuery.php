<?php
namespace App\GraphQL\Query;

use GraphQL;
use GraphQL\Type\Definition\Type;
use Folklore\GraphQL\Support\Query;

use Auth;
use App\Group;
use App\GroupUser;

class GroupsQuery extends Query
{
    protected $attributes = [
        'name' => 'groups'
    ];

    public function type()
    {
        return Type::listOf(GraphQL::type('Group'));
    }

    public function args()
    {
        return [
            'id' => ['name' => 'id', 'type' => Type::int()],
            'user_id' => ['name' => 'user_id', 'type' => Type::int()],
            'order' => ['name' => 'order', 'type' => Type::string()]
        ];
    }

    public function resolve($root, $args)
    {
        $userId = Auth::check();
        $group = Group::find($args['id']);

        if(!$group->public) {
            $groupAttendance = GroupUser::where('user_id', Auth::id())->where('group_id', $group->id)->first();

            if(is_null($groupAttendance)) {
                return null;
            }
        }

        if(isset($args['id'])) {
            return Group::where('id', $args['id'])->with('members')->get();
        }

        if(isset($args['user_id'])) {
            return GroupUser::join('groups', 'groups.id', '=', 'group_users.group_id')->with('members')->get();
        }

        if(isset($args['order'])) {
            switch($args['order']) {
                case 'desc':
                    return Group::orderBy('id', 'desc')->where('public', 1)->with('members')->get();
                case 'asc':
                default:
                    return Group::orderBy('id', 'asc')->where('public', 1)->with('members')->get();
            }
        }

        return Group::with('members')->get();
    }
}
