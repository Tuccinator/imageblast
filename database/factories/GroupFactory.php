<?php

use Faker\Generator as Faker;

$factory->define(\App\Group::class, function (Faker $faker) {
    return [
        'creator_id' => null,
        'name' => $faker->sentence,
        'description' => null,
        'public' => 1,
        'invite_code' => null
    ];
});
