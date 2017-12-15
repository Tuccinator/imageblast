<?php

use Faker\Generator as Faker;

$factory->define(\App\Image::class, function (Faker $faker) {
    return [
        'user_id' => 1,
        'name' => $faker->sentence,
        'likes' => 0,
        'dislikes' => 0,
        'path' => '/images/example.jpg',
        'image_type' => 0,
        'private' => 0
    ];
});
