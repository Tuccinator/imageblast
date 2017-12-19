<?php

use Faker\Generator as Faker;

$factory->define(\App\GroupUser::class, function (Faker $faker) {
    return [
        'user_id' => null,
        'group_id' => null,
        'invite_code' => null,
        'status' => 0,
        'status_end' => null,
        'role' => 1
    ];
});
