<?php
namespace App\GraphQL\Query;

use GraphQL;
use GraphQL\Type\Definition\Type;
use GraphQL\Type\Definition\ResolveInfo;
use Folklore\GraphQL\Support\Query;
use App\User;
use Auth;

class UsersQuery extends Query
{
    protected $attributes = [
        'name' => 'users'
    ];

    public function type()
    {
        return Type::listOf(GraphQL::type('User'));
    }

    public function args()
    {
        return [
            'id' => ['name' => 'id', 'type' => Type::int()],
            'email' => ['name' => 'email', 'type' => Type::string()],
            'groups' => ['name' => 'groups', 'type' => Type::listOf(GraphQL::type('Group'))]
        ];
    }

    public function resolve($root, $args, $context, ResolveInfo $info)
    {
        $fields = $info->getFieldSelection($depth = 3);

        $query = null;

        if(isset($args['id'])) {
            $query = User::where('id', $args['id']);
        } else if(isset($args['email'])) {
            $query = User::where('email', $args['email']);
        } else {
            return User::all();
        }

        foreach($fields as $field => $keys) {
            if($field === 'groups') {
                $query->with('groups');
            }
        }

        return $query->get();
    }
}
