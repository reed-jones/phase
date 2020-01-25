<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
Auth::routes();
Route::name('Auth.')->group(function () {
    Route::phase('/login', 'Auth\LoginController@LoginPage')->name('LoginPage');
    Route::phase('/register', 'Auth\RegisterController@RegisterPage')->name('RegisterPage');
    Route::get('/logout', 'Auth\LoginController@logout')->name('Logout');
});

Route::phase('/', 'PhaseController@HomePage');
Route::phase('/about', 'PhaseController@AboutPage');
