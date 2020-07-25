<?php

use App\User;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('login', fn () => auth()->login(factory(User::class)->create(request()->all())));
Route::post('create', fn () => factory(request()->input('model'))->create(request()->input('attributes', [])));
Route::get('csrf_token', fn () => response()->json(csrf_token()));
Route::post('artisan', function() {
    define('STDIN',fopen("php://stdin","r"));
    return Artisan::call(request()->command);
});
Route::get('logout', fn () => Auth::logout());
