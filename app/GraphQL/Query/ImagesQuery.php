<?php
namespace App\GraphQL\Query;

use GraphQL;
use GraphQL\Type\Definition\Type;
use GraphQL\Type\Definition\ResolveInfo;
use Folklore\GraphQL\Support\Query;
use App\Image;
use App\User;
use Auth;

class ImagesQuery extends Query
{
    protected $attributes = [
        'name' => 'images'
    ];

    public function type()
    {
        return Type::listOf(GraphQL::type('Image'));
    }

    public function args()
    {
        return [
            'id' => [
                'name' => 'id',
                'type' => Type::int()
            ]
        ];
    }

    public function resolve($root, $args)
    {
        $userId = Auth::id();

        if(isset($args['id'])) {

            // first check if user can see the image
            return [Image::find($args['id'])];
        }

        /**
         * Get all images by the logged in user
         *
         * TODO: Get images for all following and attended groups
         */
        return Image::where('user_id', $userId)->orderBy('id', 'desc')->with('user')->get();
    }
}
