<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use Validator;
use Illuminate\Validation\ValidationException;
use Eventviva\ImageResize;

class UserController extends Controller
{
    /**
     * Signup view
     *
     * @return View
     */
    public function signup()
    {
        return view('user.signup');
    }

    /**
     * Login view
     *
     * @return View
     */
    public function login()
    {
        return view('user.login');
    }

    /**
     * Login POST
     * GraphQL offers a "GET" request or a "POST" request (mutation). Logging in
     * does not manipulate the data but you also don't want it to be a GET request.
     *
     * @param $request Illuminate\Http\Request
     * @return json
     */
    public function loginPost(Request $request)
    {
        // validate the fields
        $this->validate($request, [
            'email' => 'required|email|exists:users',
            'password' => 'required'
        ]);

        $email = $request->email;
        $password = $request->password;

        // add invalid auth validation message
        if(!Auth::attempt(['email' => $email, 'password' => $password])) {
            return json_encode(['success' => false]);
        }

        return json_encode(['success' => true]);
    }

    /**
     * Upload an avatar. Easier through REST than mutation.
     *
     * @param $request \Illuminate\Http\Request
     * @return json
     */
    public function uploadAvatar(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'avatar' => 'required|image'
        ]);

        $avatar = $request->file('avatar');

        $validator->after(function($validator) use($avatar) {
            if(!is_null($avatar)) {
                if(!$avatar->isValid()) {
                    $validator->errors()->add('avatar', 'Avatar was unable to be uploaded.');
                }
            }
        });

        if($validator->fails()) {
            throw new ValidationException($validator);
        }

        $path = $avatar->path();
        $hash = $avatar->hashName();
        $ext = $avatar->extension();

        $savePath = public_path('avatars/' . $hash);

        $image = new ImageResize($path);
        $image->crop(100, 100);
        $image->save($savePath);

        $user = Auth::user();
        $user->avatar = 'avatars/' . $hash;
        if(!$user->save()) {
            return json_encode(['success' => false, 'message' => 'Could not save new avatar.']);
        }

        return json_encode(['success' => true, 'path' => 'avatars/' . $hash]);
    }

    /**
    * Account page view
     */
    public function account()
    {
        return view('user.account', ['user' => Auth::user()]);
    }

    /**
     * Logout the user
     *
     * @return Illuminate\Http\Response
     */
    public function logout()
    {
        Auth::logout();

        return redirect('/');
    }
}
