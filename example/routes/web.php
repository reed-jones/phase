<?php

use Illuminate\Routing\Router;
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
Route::phase('/', 'PublicController@HomePage');

Auth::routes();
Route::phase('/login', 'Auth\LoginController@LoginPage');
Route::phase('/register', 'Auth\RegisterController@RegisterPage');
Route::phase('/home', 'HomeController@DashboardPage');


// function findRouteByAction($action) {
//     $router = App::make('router');
//     $routeCollection = $router->getRoutes();
//     $groupStack = $router->getGroupStack();
//     $group = end($groupStack);
//      return  isset($group['namespace']) && strpos($action, '\\') !== 0
//             ? $group['namespace'].'\\'.$action : $action;
// }

// // $router = App::make('router');
// // $routeCollection = $router->getRoutes();

// // dd(
// //     $routeCollection->getByAction(findRouteByAction('HomeController@DashboardPage'))->uri()
// // );
