<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Product;
use App\Models\User;
use Faker\Generator as Faker;

$factory->define(Product::class, function (Faker $faker) {
    return [
        'user_id' => factory(User::class),
        'quantity' => $faker->numberBetween(1, 24),
        'price' => $faker->numberBetween(1000, 50000),
        'title' => $faker->sentence($faker->numberBetween(1, 5)),
        'description' => $faker->paragraph($faker->numberBetween(2, 10))
    ];
});
