<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Notice;
use App\Models\User;
use Faker\Generator as Faker;

$factory->define(Notice::class, function (Faker $faker) {
    return [
        'user_id' => factory(User::class),
        'title' => $faker->sentence($faker->numberBetween(1, 5)),
        'content' => $faker->paragraph($faker->numberBetween(2, 10))
    ];
});
