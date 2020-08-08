<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Phased\State\Facades\Vuex;

class CounterController extends Controller
{
    public function increment($number) {
        // Set the vuex state using the 'VuexLoaders'
        Vuex::load('user', [ 'counter' => $number + 1 ]);

        // ->vuex() is just an alias of ->phase()
        return response()->vuex();
    }

    public function decrement($number) {
        // set the vuex module state directly
        Vuex::module('user', [ 'counter' => $number - 1 ]);
        return response()->phase();
    }
}
