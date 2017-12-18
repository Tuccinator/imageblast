<?php
namespace App\GraphQL\Query;

use GraphQL;
use GraphQL\Type\Definition\Type;
use Folklore\GraphQL\Support\Query;

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
        if(isset($args['id'])) {
            return Group::where('id', $args['id'])->with('members')->get();
        }

        if(isset($args['user_id'])) {
            return GroupUser::join('groups', 'groups.id', '=', 'group_users.group_id')->with('members')->get();
        }

        if(isset($args['order'])) {
            switch($args['order']) {
                case 'desc':
                    return Group::orderBy('id', 'desc')->with('members')->get();
                case 'asc':
                default:
                    return Group::orderBy('id', 'asc')->with('members')->get();
            }
        }

        return Group::with('members')->get();
    }
}
