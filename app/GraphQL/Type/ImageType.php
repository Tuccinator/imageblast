<?php
namespace App\GraphQL\Type;

use GraphQL;
use GraphQL\Type\Definition\Type;
use Folklore\GraphQL\Support\Type as GraphQLType;

class ImageType extends GraphQLType
{
    protected $attributes = [
        'name' => 'Image',
        'description' => 'A image'
    ];

    public function fields()
    {
        return [
            'id' => [
                'type' => Type::nonNull(Type::int()),
                'description' => 'Image ID'
            ],
            'path' => [
                'type' => Type::string(),
                'description' => 'Path of image',
            ],
            'name' => [
                'type' => Type::string(),
                'description' => 'Name of image'
            ],
            'likes' => [
                'type' => Type::int(),
                'description' => 'Amount of likes on image'
            ],
            'dislikes' => [
                'type' => Type::int(),
                'description' => 'Amount of dislikes on image'
            ],
            'user' => [
                'type' => GraphQL::type('User'),
                'description' => 'Creator of the image'
            ]
        ];
    }
}
