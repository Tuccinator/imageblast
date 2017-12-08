<?php
namespace App\GraphQL\Type;

use GraphQL;
use GraphQL\Type\Definition\Type;
use Folklore\GraphQL\Support\Type as GraphQLType;

class GroupType extends GraphQLType
{
    protected $attributes = [
        'name' => 'Group',
        'description' => 'A group'
    ];

    public function fields()
    {
        return [
            'id' => [
                'type' => Type::nonNull(Type::int()),
                'description' => 'Group ID'
            ],
            'members' => [
                'type' => Type::listOf(GraphQL::type('User')),
                'description' => 'Members of group'
            ],
            'name' => [
                'type' => Type::string(),
                'description' => 'Group name'
            ],
            'description' => [
                'type' => Type::string(),
                'description' => 'Group description'
            ]
        ];
    }
}
