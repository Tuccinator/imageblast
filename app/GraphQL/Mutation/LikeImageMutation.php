<?php
namespace App\GraphQL\Mutation;

use GraphQL;
use GraphQL\Type\Definition\Type;
use Folklore\GraphQL\Support\Mutation;
use App\User;
use App\Image;
use App\ImageLike;
use Auth;

class LikeImageMutation extends Mutation
{
    protected $attributes = [
        'name' => 'likeImage'
    ];

    public function type()
    {
        return GraphQL::type('Image');
    }

    public function args()
    {
        return [
            'id' => [
                'name' => 'id',
                'type' => Type::int(),
                'rules' => ['required', 'exists:images']
            ],
            'type' => [
                'name' => 'type',
                'type' => Type::string(),
                'rules' => ['required', 'in:1,2']
            ]
        ];
    }

    public function resolve($root, $args)
    {
        $imageId = $args['id'];
        $type = (int)$args['type'];
        $image = Image::find($imageId);

        // make sure the user is logged in or the image is public
        if(!Auth::check() || $image->isPrivate()) {
            return null;
        }

        // check if the user has already liked the image
        $likeExists = ImageLike::where('image_id', $imageId)->where('user_id', Auth::id())->first();

        if(!is_null($likeExists)) {

            // reverse a previous like/dislike
            if($likeExists->type === $type) {
                $image->reverseLike($likeExists->type);
                $image->save();

                $likeExists->delete();

                return $image;
            }

            $image->reverseLike($likeExists->type);
            $likeExists->delete();
        }

        // create a new like and attach to image
        $like = new ImageLike;
        $like->image_id = $imageId;
        $like->user_id = Auth::id();
        $like->type = $type;

        if(!$like->save()) {
            return null;
        }

        $image->addLike($type);
        $image->save();

        return $image;
    }
}
