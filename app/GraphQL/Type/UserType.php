<?php
namespace App\GraphQL\Type;

use GraphQL;
use GraphQL\Type\Definition\Type;
use Folklore\GraphQL\Support\Type as GraphQLType;

class UserType extends GraphQLType
{
    protected $attributes = [
        'name' => 'User',
        'description' => 'A user'
    ];

    public function fields()
    {
        return [
            'id' => [
                'type' => Type::nonNull(Type::int()),
                'description' => 'ID of user'
            ],
            'username' => [
                'type' => Type::string(),
                'description' => 'Username of user'
            ],
            'email' => [
                'type' => Type::string(),
                'description' => 'Email of user'
            ],
            'groups' => [
                'type' => Type::listOf(GraphQL::type('Group')),
                'description' => 'Groups the user has joined'
            ]
        ];
    }
}
