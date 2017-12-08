<?php
namespace App\GraphQL\Mutation;

use GraphQL;
use GraphQL\Type\Definition\Type;
use Folklore\GraphQL\Support\Mutation;
use App\User;
use Auth;

class CreateUserMutation extends Mutation
{
    protected $attributes = [
        'name' => 'createUser'
    ];

    public function type()
    {
        return GraphQL::type('User');
    }

    public function args()
    {
        return [
            'username' => [
                'name' => 'username',
                'type' => Type::string(),
                'rules' => ['required', 'unique:users']
            ],
            'email' => [
                'name' => 'email',
                'type' => Type::string(),
                'rules' => ['required', 'email', 'unique:users']
            ],
            'password' => [
                'name' => 'password',
                'type' => Type::string(),
                'rules' => ['required']
            ]
        ];
    }

    public function resolve($root, $args)
    {
        if(Auth::check()) {
            return Auth::user();
        }

        $user = new User;
        $user->username = $args['username'];
        $user->email = $args['email'];
        $user->password = password_hash($args['password'], PASSWORD_DEFAULT);

        if(!$user->save()) {
            return null;
        }

        Auth::attempt(['email' => $user->email, 'password' => $args['password']]);

        return $user;
    }
}
