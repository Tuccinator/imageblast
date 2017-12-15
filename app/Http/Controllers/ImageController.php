<?php
namespace App\Http\Controllers;

use Validator;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use App\User;
use App\Image;

class ImageController extends Controller
{
    /**
     * Upload an image. Easier through REST compared to mutation.
     *
     * @param $request \Illuminate\Http\Request
     * @return json
     */
    public function upload(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'image' => 'required|image',
            'privacy' => [
                'required',
                Rule::in(['0', '1', '2'])
            ]
        ]);

        $image = $request->file('image');

        $validator->after(function($validator) use($image) {
            if(!is_null($image)) {
                if(!$image->isValid()) {
                    $validator->errors()->add('image', 'Image must be valid.');
                }
            }
        });

        if($validator->fails()) {
            throw new ValidationException($validator);
        }

        $path = $image->store('images', 'public');

        $imageModel = new Image;
        $imageModel->user_id = Auth::id();
        $imageModel->group_id = null;
        $imageModel->name = '';
        $imageModel->likes = 0;
        $imageModel->dislikes = 0;
        $imageModel->path = $path;
        $imageModel->image_type = 0;
        $imageModel->private = $request->privacy;

        if(!$imageModel->save()) {
            return json_encode(['success' => false, 'path' => $path]);
        }

        return json_encode(['success' => true, 'path' => $path]);
    }
}
