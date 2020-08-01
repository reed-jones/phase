<?php

use App\Models\User;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/** Creates a user & will log in */
Route::get('login', function() {
    return tap(factory(User::class)->create(request()->all()), fn ($user) => auth()->login($user));
});

/** Creates a new model with the given attributes */
Route::post('create', fn () => factory(request()->input('model'))->create(request()->input('attributes', [])));

/** Retrieves the csrf token */
Route::get('csrf_token', fn () => response()->json(csrf_token()));

/** Runs artisan commands */
Route::post('artisan', function() {
    define('STDIN',fopen("php://stdin","r"));
    return Artisan::call(request()->command);
});

/** Logs out current user */
Route::get('logout', fn () => Auth::logout());
