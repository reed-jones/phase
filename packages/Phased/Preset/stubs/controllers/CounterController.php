<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Phased\Routing\Facades\Phase;
use Phased\State\Facades\Vuex;

class CounterController
{
    public function CounterPage($count)
    {
        Vuex::module('app', [
            'count' => (int) $count,
        ]);

        return Phase::view();
    }

    public function SetCount(Request $request)
    {
        Vuex::module('app', [
            'count' => $request->count,
        ]);

        return response()->vuex();
    }
}
