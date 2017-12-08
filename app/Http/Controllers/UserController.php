<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;

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
