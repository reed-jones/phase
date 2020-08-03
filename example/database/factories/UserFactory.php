<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Notice;
use App\Models\Product;
use App\Models\User;
use Faker\Generator as Faker;
use Illuminate\Support\Str;

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| This directory should contain each of the model factory definitions for
| your application. Factories provide a convenient way to generate new
| model instances for testing / seeding your application's database.
|
*/

$factory->define(User::class, fn (Faker $faker) => [
    'name' => $faker->name,
    'email' => $faker->unique()->safeEmail,
    'phone' => $faker->phoneNumber,
    'bio' => $faker->paragraph,
    'email_verified_at' => now(),
    'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
    'remember_token' => Str::random(10),
]);


$factory->afterCreatingState(App\Models\User::class, 'makes-notices', fn ($user, $faker) => $user->notices()->createMany(
    factory(Notice::class, $faker->numberBetween(1, 25))->make()->toArray()
));

$factory->afterCreatingState(App\Models\User::class, 'sells-products', fn ($user, $faker) => $user->products()->createMany(
    factory(Product::class, $faker->numberBetween(1, 25))->make()->toArray()
));
